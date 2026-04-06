<?php

namespace App\Http\Controllers;

use App\Dashboard\NeedValidationWidget;
use App\Dashboard\NewTicketsWidget;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    private function alter_add_name(&$item, $key)
    {
        $item = [
            'count' => $item,
            'name' => TicketStatus::from($key)->getName()
        ];
    }

    public function dashboard()
    {
        $role = Auth::user()->role;
        $statistics = [
            new NewTicketsWidget(),
            new NeedValidationWidget(),
        ];
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
        return view('dashboard', ['statistics' => $statistics, 'role' => $role, 'countByStatus' => $countByStatus]);
    }
}
