<?php

namespace App\Http\Controllers;

use App\Enums\TicketKind;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TimeLog;
use App\Models\User;
use Auth;
use Closure;
use DateInterval;
use DateTimeImmutable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function list(Request $request)
    {
        return view('tickets.list', ["canCreate" => Auth::user()->canCreateTickets()]);
    }

    public function apiList(Request $request) {
        $status = TicketStatus::tryFrom($request['status'] ?? null);
        $type = TicketType::tryFrom($request['type'] ?? null);
        $validated = $request->validate([
            'priority-sort' => ['nullable', 'string', 'in:asc,desc,none'],
            'search' => ['nullable', 'string'],
            'in-project' => ['nullable', 'integer', 'exists:projects,id'],
            'page'          => ['nullable', 'integer', 'min:1'],
        ]);
        $query = Ticket::with('project');
        // TODO remove the thingy after testy test
        if (Auth::user() != null && Auth::user()->role != UserRole::ADMIN && !isset($validated['in-project'])) {
            $query->whereHas('project.members', function ($q) {
                $q->where('user_id', '=', Auth::id());
            });
        }
        if ($status) {
            $query->where('status', '=', $status);
        }
        if ($type) {
            $query->where('type', '=', $type);
        }
        if (isset($validated['priority-sort']) && $validated['priority-sort'] != 'none') {
            $cases = TicketPriority::cases();
            $orderSql = 'CASE priority ';
            foreach ($cases as $index => $case) {
                $orderSql .= "WHEN '{$case->value}' THEN {$index} ";
            }
            $orderSql .= 'END';
            $query->orderByRaw("$orderSql {$validated['priority-sort']}");
        }
        if (isset($validated['search'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('title', 'like', '%' . $validated['search'] . '%');
                if (!isset($validated['in-project'])) {
                    $q->orWhereHas('project', fn($q) => $q->where('issue_prefix', '=', $validated['search']));
                }
            });
        }
        if (isset($validated['in-project'])) {
            $query->where('project_id', '=', $validated['in-project']);
        }
        if ($request->boolean("assigned-to-me")) {
            $query->whereRelation('assignedTo', 'id', '=', Auth::id());
        }
        $tickets = $query->paginate(15, ['*'], 'page', $validated['page'] ?? 1);
        $tickets->through(function (Ticket $ticket) {
            return [
                ...$ticket->makeHidden('project', 'previous_status', 'refuse_reason')->toArray(),
                'user' => [
                    'id'        => $ticket->createdBy->id,
                    'full_name' => $ticket->createdBy->fullName(),
                ],
                'slug' => $ticket->project->issue_prefix . '-' . $ticket->local_id,
                'project_route' => route('projects.view', $ticket->project->id),
                'ticket_route' => route('tickets.view', $ticket->id),
                'priority' => $ticket->priority->getName(),
                'status' => $ticket->status->getName(),
                'type' => $ticket->type?->getName() ?? 'Unset',
                'kind' => $ticket->kind->getName(),
            ];
        });

        return response()->json($tickets);
    }

    public function create()
    {
        if (Auth::user()->role == UserRole::CLIENT) return view('error', [
            "message" => "You are unauthorized to create tickets.",
            "goBack" => route("tickets.list")
        ]);
        $query = Project::query();
        if (Auth::user()->role != UserRole::ADMIN) {
            $query->whereRelation('members', 'id', '=', Auth::id());
        }
        return view('tickets.create', ['projects' => $query->get()]);
    }

    public function view($id)
    {
        $ticket = Ticket::with(['project', 'createdBy'])->find($id);
        if (!$ticket) return view('error', [
            "message" => "Unknown ticket.",
            "goBack" => route("tickets.list")
        ]);

        if (!$ticket->hasAccess()) return view('error', [
            "message" => "You don't have access to this ticket.",
            "goBack" => route("tickets.list")
        ]);
        $logsByUser = $ticket->logs()
            ->with('user')
            ->get()
            ->groupBy(fn($log) => $log->user_id)
            ->map(fn($logs) => [
                'id'    => $logs->first()->user_id,
                'full_name'  => $logs->first()->user->fullName(),
                'time_spent' => TimeLog::formatDuration($logs->sum('time_spent')),
            ])
            ->values();
        $totalTime = $ticket->logs()->sum('time_spent');
        $clientValidation = Auth::user()->role == UserRole::CLIENT && $ticket->status == TicketStatus::WAITING_FOR_VALIDATION;
        return view('tickets.view', [
                'ticket' => $ticket,
                'logs' => $logsByUser,
                'totalTime' => TimeLog::formatDuration($totalTime),
                'clientValidation' => $clientValidation,
                'editable' => $ticket->canEdit(),
            ]
        );
    }

    public function edit($id)
    {
        $ticket = Ticket::query()->find($id);
        if (!$ticket) return view('error', [
            "message" => "Unknown ticket.",
            "goBack" => route("tickets.list")
        ]);
        if (!$ticket->hasAccess() || !$ticket->canEdit()) {
            return view('error', [
                "message" => "You are unauthorized to view this ticket.",
                "goBack" => route("tickets.list")
            ]);
        }
        $except = [];
        if (Auth::user()->role == UserRole::DEVELOPER) {
            $except = [TicketStatus::REFUSED, TicketStatus::ACCEPTED, TicketStatus::WAITING_FOR_VALIDATION];
        }
        return view('tickets.edit', ['ticket' => $ticket, 'statusOptions' => TicketStatus::keyToName($except)]);
    }

    public function log($id)
    {
        if (Auth::user()->role == UserRole::CLIENT) return view('error', [
            "message" => "You are unauthorized to log in this ticket.",
            "goBack" => route("tickets.list")
        ]);
        $ticket = Ticket::query()->find($id);
        if (!$ticket) return view('error', [
            "message" => "Unknown ticket.",
            "goBack" => route("tickets.list")
        ]);
        if (!$ticket->hasAccess(Auth::user())) return view('error', [
            "message" => "You don't have access to this ticket.",
            "goBack" => route("tickets.list")
        ]);
        return view('tickets.log', ['ticket' => $ticket]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role == UserRole::CLIENT) abort(403);
        $validated = $request->validate([
            'title' => 'required',
            'project' => ['required', 'exists:projects,id'],
            'ticket-kind' => Rule::enum(TicketKind::class),
            'ticket-type' => Rule::enum(TicketType::class),
            'priority' => Rule::enum(TicketPriority::class),
            'description' => 'required',
        ]);

        if (!Project::find($validated['project'])->hasAccess(Auth::user())) abort(403);

        $ticket = Ticket::create([
            'title' => $validated['title'],
            'created_by' => auth()->user()->id,
            'project_id' => $validated['project'],
            'kind' => $validated['ticket-kind'],
            'type' => $validated['ticket-type'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
        ]);

        if ($ticket->type == TicketType::BILLED) $ticket->update([
            'status' => TicketStatus::WAITING_FOR_VALIDATION,
            'type' => null,
            'previous_status' => TicketStatus::NEW,
        ]);

        return redirect()->route('tickets.view', $ticket->id);
    }

    public function storeLog(Request $request, $id)
    {
        if (Auth::user()->role == UserRole::CLIENT) abort(403);
        $ticket = Ticket::query()->find($id);
        if (!$ticket) abort(404);
        if (!$ticket->hasAccess()) abort(403);
        $validated = $request->validate([
            'start' => ["nullable", "date"],
            'time-spent' => ["required", "numeric"],
            'comment' => ["nullable", "string"],
        ]);

        try {
            $start = $validated['start'] ? new DateTimeImmutable($validated['start']) : (new DateTimeImmutable('now'))->sub(new DateInterval('PT' . $validated['time-spent'] . 'M'));
        } catch (Exception $e) {
            abort(400, $e->getMessage());
        }

        TimeLog::create([
            'ticket_id' => $id,
            'user_id' => Auth::user()->id,
            'started_at' => $start,
            'time_spent' => $validated['time-spent'],
            'comment' => $validated['comment'] ?? "",
        ]);
        return redirect()->route('tickets.view', $id);
    }

    public function apiAssignTo(Request $request)
    {
        $validated = $request->validate([
            "id" => ["required", "exists:tickets,id"],
            "member_email" => ["required", "email", "exists:users,email", function ($attribute, $value, $fail) use ($request) {
                $user = User::where('email', $value)->first();
                if ($user && Ticket::find($request->id)->assignedTo()->where('user_id', $user->id)->exists()) {
                    $fail('This member is already assigned to this ticket.');
                }
                if ($user->role == UserRole::CLIENT) {
                    $fail('You cannot assign a ticket to a client.');
                }
            },],
        ]);

        $user = User::where('email', '=', $validated['member_email'])->first();
        Ticket::find($validated['id'])->assignedTo()->attach($user->id);
        return response()->json([
            "success" => true,
            "message" => "Member assigned.",
            "member" => [
                "full_name" => $user->fullName(),
                "id" => $user->id,
            ]
        ], 201);
    }

    public function apiUnassign(Request $request)
    {
        $validated = $request->validate([
            "id" => ["required", "exists:tickets,id"],
            'member_id' => ["required", "exists:users,id"],
        ]);
        $ticket = Ticket::find($validated['id']);
        if (!$ticket->canEdit(Auth::user())) abort(403);
        $ticket->assignedTo()->detach($validated['member_id']);
        return response()->json();
    }

    public function apiGetLogs(Request $request) {
        $validated = $request->validate([
            "ticket_id" => ["required", "exists:tickets,id"],
            'user_id' => ["required", "exists:users,id"],
        ]);
        $ticket = Ticket::with('logs')->find($validated['ticket_id']);
        if (!$ticket->hasAccess()) abort(403);
        $logs = $ticket->logs()->where('user_id', '=', $validated['user_id'])->get();
        $logs = $logs->map(function ($log) {
            $array = $log->toArray();
            $array['time_spent'] = TimeLog::formatDuration($array['time_spent']);
            return $array;
        });
        return response()->json($logs);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::query()->find($id);
        if (!$ticket) abort(404);
        if (!$ticket->hasAccess() || !$ticket->canEdit()) abort(403);
        $validated = $request->validate([
            'title' => 'required',
            'ticket-kind' => Rule::enum(TicketKind::class),
            'priority' => Rule::enum(TicketPriority::class),
            'ticket-type' => [function (string $attribute, mixed $value, Closure $fail) use ($ticket) {
                if (empty($value)) {
                    if ($ticket->type != null) $fail("Type cannot be unset.");
                    return;
                }
                if (TicketType::tryFrom($value) == null) $fail("Invalid Ticket type.");
            }],
            'description' => 'required',
        ]);
        // check if it was changed to billed
        $type = $validated['ticket-type'] == TicketType::BILLED->value && $ticket->type != TicketType::BILLED ? null : $validated['ticket-type'];
        $updateQuery = [
            'title' => $validated['title'],
            'kind' => $validated['ticket-kind'],
            'priority' => $validated['priority'],
            'type' => $type,
            'description' => $validated['description'],
        ];
        // if it was changed to billed set it to Waiting For Validation
        if ($type == null) {
            $updateQuery['previous_status'] = $ticket->status;
            $updateQuery['status'] = TicketStatus::WAITING_FOR_VALIDATION;
        }
        $ticket->update($updateQuery);
        return redirect()->route('tickets.view', $ticket->id);
    }

    public function destroy(Request $request)
    {
        $role = Auth::user()->role;
        if ($role == UserRole::CLIENT) return view('error', [
            'message' => "You aren't authorized to delete tickets.",
            'goBack' => route("tickets.list")
        ]);
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:tickets,id'],
        ]);
        $ticket = Ticket::query()->find($validated['id']);
        if (!$ticket->canEdit()) return view('error', [
            'message' => "You cannot delete this ticket.",
            'goBack' => route("tickets.list")
        ]);
        $ticket->delete();
        return redirect()->back();
    }

    public function clientValidation(Request $request, $id) {
        if (Auth::user()->role != UserRole::CLIENT) abort(403);
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        $ticket = Ticket::query()->find($id);
        if (!$ticket) abort(404);
        $accepted = $request->has('accept');
        if ($accepted) {
            $ticket->update([
                'status' => $ticket->previous_status ?? TicketStatus::ACCEPTED,
                'type' => TicketType::BILLED,
                'previous_status' => null,
            ]);
        } else {
            $ticket->update([
                'status' => TicketStatus::REFUSED,
                'type' => null,
                'refuse_reason' => $validated['reason'] ?? '',
            ]);
        }
        return redirect()->route('tickets.view', $id);
    }
}
