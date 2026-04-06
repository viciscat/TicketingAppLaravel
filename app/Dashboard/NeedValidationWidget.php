<?php

namespace App\Dashboard;

use App\Dashboard\DashboardWidget;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use Auth;

class NeedValidationWidget implements DashboardWidget
{

    public function iconPath(): string
    {
        return "icons/ticket_validation.png";
    }

    public function title(): string
    {
        return "Tickets awaiting your validation";
    }

    public function value(): string
    {
        if (Auth::user()->role != UserRole::CLIENT) return "0";
        $query = Ticket::where("status", "=", TicketStatus::WAITING_FOR_VALIDATION);
        $query->whereHas('project.members', function ($query) {
            $query->where("users_id", "=", Auth::user()->id);
        });
        return $query->count();
    }

    public function clickRoute(): string|null
    {
        return route("tickets.list", ["status" => TicketStatus::WAITING_FOR_VALIDATION]);
    }
}
