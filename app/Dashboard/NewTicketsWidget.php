<?php

namespace App\Dashboard;

use App\Dashboard\DashboardWidget;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use Auth;

class NewTicketsWidget implements DashboardWidget
{

    public function iconPath(): string
    {
        return "icons/ticket.png";
    }

    public function title(): string
    {
        return "New Tickets";
    }

    public function value(): string
    {
        $query = Ticket::where("status", "=", TicketStatus::NEW);
        if (Auth::user()->role != UserRole::ADMIN) {
            $query->whereHas('project.members', function ($query) {
                $query->where("users_id", "=", Auth::user()->id);
            });
        }
        return $query->count();
    }

    public function clickRoute(): string|null
    {
        return route("tickets.list", ["status" => TicketStatus::NEW]);
    }
}
