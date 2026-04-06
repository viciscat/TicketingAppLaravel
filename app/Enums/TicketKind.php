<?php

namespace App\Enums;

enum TicketKind: string
{
    use Named;
    case ISSUE = 'issue';
    case TASK = 'task';

    public function getName(): string
    {
        return match ($this) {
            self::ISSUE => "Issue",
            self::TASK => "Task"
        };
    }
}
