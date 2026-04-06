<?php

namespace App\Enums;

enum TicketStatus: string
{
    use Named;
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
    case REFUSED = 'refused';
    case ACCEPTED = 'accepted';
    case WAITING_FOR_VALIDATION = 'waiting_for_validation';

    public function getName(): string
    {
        return match ($this) {
            self::NEW => "New",
            self::IN_PROGRESS => "In Progress",
            self::FINISHED => "Finished",
            self::REFUSED => "Refused",
            self::ACCEPTED => "Accepted",
            self::WAITING_FOR_VALIDATION => "Needs client validation",
        };
    }
}
