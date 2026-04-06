<?php

namespace App\Http\Controllers;

use App\Enums\TicketType;
use App\Models\Contract;
use App\Models\Project;
use App\Models\TimeLog;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ProjectController extends Controller
{
    public function list()
    {
        return view('projects.list', ['projects' => Project::all()]);
    }

    public function create()
    {
        return view('projects.create');
    }

    public function view($id)
    {
        $project = Project::with(['tickets', 'contract'])->find($id);
        $timeIncluded = TimeLog::formatDuration($project->tickets()
            ->where('type', '=', TicketType::INCLUDED)
            ->join('time_logs', 'tickets.id', '=', 'time_logs.ticket_id')
            ->sum('time_logs.time_spent')
        );
        $timeBilled = TimeLog::formatDuration($project->tickets()
            ->where('type', '=', TicketType::BILLED)
            ->join('time_logs', 'tickets.id', '=', 'time_logs.ticket_id')
            ->sum('time_logs.time_spent')
        );
        return view('projects.view', ['project' => $project, 'timeIncluded' => $timeIncluded, 'timeBilled' => $timeBilled]);
    }

    public function edit($id)
    {
        return view('projects.edit', ['project' => Project::with(['contract'])->find($id)]);
    }

    public function store(Request $request)
    {
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
        // TODO check it's not null
        $project = Project::find($id);
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

        Project::find($validated['id'])->delete();
        return redirect()->route('projects.list');
    }

    public function apiAddMember(Request $request)
    {
        $validated = $request->validate([
            "id" => ["required", "exists:projects,id"],
            "member_email" => ["required", "email", "exists:users,email"],
        ]);

        $user = User::where('email', '=', $validated['member_email'])->first();
        Project::find($validated['id'])->members()->attach($user->id);
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
        Project::find($validated['id'])->members()->detach($validated['member_id']);
        return response()->json();
    }
}
