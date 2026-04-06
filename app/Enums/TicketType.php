<?php

namespace App\Enums;

enum TicketType: string
{
    use Named;
    case INCLUDED = 'included';
    case BILLED = 'billed';

    public function getName(): string
    {
        return match ($this) {
            self::INCLUDED => 'Included',
            self::BILLED => 'Billed',
        };
    }
}
