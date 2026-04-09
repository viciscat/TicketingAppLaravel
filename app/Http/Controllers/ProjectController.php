<?php

namespace App\Http\Controllers;

use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Models\Contract;
use App\Models\Project;
use App\Models\TimeLog;
use App\Models\User;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function list()
    {
        $query = Project::query();
        if (Auth::user()->role != UserRole::ADMIN) {
            $query->whereRelation('members', 'id', '=', Auth::user()->id);
        }
        return view('projects.list', ['projects' => $query->get(), 'canCreate' => Auth::user()->canCreateProjects()]);
    }

    public function create()
    {
        if (!Auth::user()->canCreateProjects()) return view('error', [
            "message" => "You are not allowed to create projects.",
            "goBack" => route('projects.list')
        ]);
        return view('projects.create');
    }

    public function view($id)
    {
        $project = Project::with(['tickets', 'contract'])->find($id);
        if (!$project) return view('error', [
            "message" => "Project not found.",
            "goBack"=> route('projects.list'),
        ]);
        if (!$project->hasAccess(Auth::user())) return view('error', [
            "message" => "You do not have permission to view this project.",
            "goBack"=> route('projects.list'),
        ]);
        $timeIncluded = $project->tickets()
            ->where('type', '=', TicketType::INCLUDED)
            ->join('time_logs', 'tickets.id', '=', 'time_logs.ticket_id')
            ->sum('time_logs.time_spent');
        $timeIncludedFormatted = TimeLog::formatDuration($timeIncluded);
        $timeBilled = TimeLog::formatDuration($project->tickets()
            ->where('type', '=', TicketType::BILLED)
            ->join('time_logs', 'tickets.id', '=', 'time_logs.ticket_id')
            ->sum('time_logs.time_spent')
        );
        $overTime = $timeIncluded > $project->contract->included_hours * 60;
        return view('projects.view', [
            'project' => $project,
            'timeIncluded' => $timeIncludedFormatted,
            'timeBilled' => $timeBilled,
            'overTime' => $overTime,
            'canEdit'=> $project->canEdit(Auth::user()),
            'canCreateTicket' => Auth::user()->canCreateTickets(),
        ]);
    }

    public function edit($id)
    {
        $project = Project::with(['contract'])->find($id);
        if (!$project) return view('error', [
            "message" => "Project not found.",
            "goBack"=> route('projects.list'),
        ]);
        if (!$project->canEdit(Auth::user())) return view('error', [
            "message" => "You do not have permission to edit this project.",
            "goBack"=> route('projects.list'),
        ]);
        return view('projects.edit', ['project' => $project]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canCreateProjects()) abort(403);
        $validated = $request->validate([
            'name' => 'required',
            'issue-prefix' => ['required', 'max:4', 'min:2'],
            'contract' => ['required', 'mimetypes:application/pdf'],
            'included-hours' => ['required', 'numeric', 'min:1', 'integer'],
            'extra-hourly-rate' => ['required', 'numeric', 'decimal:0,2'],
            'collaborators' => [function (string $attribute, mixed $value, Closure $fail) {
                if (empty($value)) return;
                foreach (explode(';', $value) as $email) {
                    if (!User::where('email', '=', $email)->exists()) $fail("User '$email' not found.");
                }
            }],
        ]);

        $filePath = $this->createContractPath($request->file('contract'));
        $request->file('contract')->storeAs('contracts', $filePath);
        $contract = Contract::create([
            'file' => $filePath,
            'included_hours' => $validated['included-hours'],
            'extra_hourly_rate' => $validated['extra-hourly-rate'],
        ]);
        $project = Project::create([
            'name' => $validated['name'],
            'issue_prefix' => $validated['issue-prefix'],
            'contract_id' => $contract->id,
        ]);
        foreach (explode(";", $validated['collaborators']) as $collaborator) {
            $user = User::where('email', '=', $collaborator)->first();
            if ($user) $project->members()->attach($user->id);
        }
        return redirect()->route('projects.view', $project->id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'issue-prefix' => ['required', 'max:4', 'min:2'],
            'contract' => ['nullable', 'mimetypes:application/pdf'],
            'included-hours' => ['required_with:contract', 'numeric', 'min:1', 'integer'],
            'extra-hourly-rate' => ['required_with:contract', 'numeric', 'decimal:0,2'],
        ]);
        $project = Project::find($id);
        if (!$project) abort(404);
        if (!$project->canEdit(Auth::user())) abort(403);
        $contract = null;
        if (isset($validated['contract'])) {
            $filePath = $this->createContractPath($request->file('contract'));
            $request->file('contract')->storeAs('contracts', $filePath);
            $contract = Contract::create([
                'file' => $filePath,
                'included_hours' => $validated['included-hours'],
                'extra_hourly_rate' => $validated['extra-hourly-rate'],
            ]);
        }

        $update = [
            'name' => $validated['name'],
            'issue_prefix' => $validated['issue-prefix']
        ];
        if (isset($contract)) $update['contract_id'] = $contract->id;
        $project->update($update);
        return redirect()->route('projects.view', $id);
    }

    private function createContractPath(UploadedFile $file)
    {
        $filePath = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        return $filePath . '_' . time() . '.' . $extension;

    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            "id" => ["required", "exists:projects,id"],
        ]);

        $project = Project::find($validated['id']);
        if (!$project->canEdit(Auth::user())) abort(403);
        $project->delete();
        return redirect()->route('projects.list');
    }

    public function viewContract($id) {
        $contract = Contract::find($id);
        if (!$contract) abort(404);
        return response()->stream(function () use ($contract) {
            echo Storage::disk('local')->get("contracts/" . $contract->file);
        }, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $contract->file . '"',
        ]);
    }

    public function apiAddMember(Request $request)
    {
        $validated = $request->validate([
            "id" => ["required", "exists:projects,id"],
            "member_email" => ["required", "email", "exists:users,email"],
        ]);
        $project = Project::find($validated['id']);
        if (!$project->canEdit(Auth::user())) abort(403);

        $user = User::where('email', '=', $validated['member_email'])->first();
        $project->members()->attach($user->id);
        return response()->json([
            "success" => true,
            "message" => "Member added.",
            "member" => [
                "full_name" => $user->fullName(),
                "role" => $user->role->getName(),
                "id" => $user->id,
            ]
        ], 201);
    }

    public function apiRemoveMember(Request $request)
    {
        $validated = $request->validate([
            "id" => ["required", "exists:projects,id"],
            'member_id' => ["required", "exists:users,id"],
        ]);
        $project = Project::find($validated['id']);
        if (!$project->canEdit(Auth::user())) abort(403);
        $project->members()->detach($validated['member_id']);
        return response()->json();
    }
}
