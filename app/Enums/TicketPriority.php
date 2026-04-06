<?php

namespace App\Enums;

enum TicketPriority: string
{
    use Named;
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function getName(): string
    {
        return match ($this) {
            self::LOW => "Low",
            self::MEDIUM => "Medium",
            self::HIGH => "High",
            self::URGENT => "Urgent",
        };
    }

    public function getCssColor(): string
    {
        return match ($this) {
            self::LOW => 'green',
            self::MEDIUM => 'blue',
            self::HIGH => 'yellow',
            self::URGENT => 'red',
        };
    }
}
