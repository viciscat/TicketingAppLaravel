<?php

namespace App\Http\Controllers;

use App\Dashboard\NeedValidationWidget;
use App\Dashboard\NewTicketsWidget;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TimeLog;
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $role = Auth::user()->role;
        $statistics = null;
        switch ($role) {
            case UserRole::ADMIN:
                $statistics = [new NewTicketsWidget()];
                break;
            case UserRole::DEVELOPER:
                $statistics = [new NewTicketsWidget()];
                break;
            case UserRole::CLIENT:
                $statistics = [new NewTicketsWidget(), new NeedValidationWidget()];
        }
        $query = Ticket::selectRaw('status, COUNT(*) as count')
            ->groupBy('status');
        if ($role != UserRole::ADMIN) {
            $query->whereHas('project.members', function ($q) {
                $q->where('id', '=', Auth::id());
            });
        }
        $countByStatus = [];
        foreach ($query->pluck('count', 'status')->toArray() as $status => $count) {
            $countByStatus[] = [
                'status' => TicketStatus::from($status)->getName(),
                'count' => $count
            ];
        }
        $query = Project::query();
        if ($role != UserRole::ADMIN) {
            $query->whereRelation("members", "id", "=", Auth::id());
        }
        $projectsOvertime = $query->with('contract')
            ->get()
            ->filter(function ($project) {
                $totalTimeSpent = TimeLog::whereHas('ticket', fn($q) => $q->where('project_id', $project->id)->where("type", '=', TicketType::INCLUDED))
                    ->sum('time_spent');

                return $totalTimeSpent > ($project->contract->included_hours * 60);
            });
        return view('dashboard', [
            'statistics' => $statistics,
            'role' => $role,
            'countByStatus' => $countByStatus,
            'projectsOvertime' => $projectsOvertime,
            ]);
    }
}
