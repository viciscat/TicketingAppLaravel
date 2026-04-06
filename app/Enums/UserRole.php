<?php

namespace App\Enums;

enum UserRole: string
{
    use Named;
    case ADMIN = 'admin';
    case DEVELOPER = 'developer';
    case CLIENT = 'client';

    public function getName(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::DEVELOPER => 'Developer',
            self::CLIENT => 'Client',
        };
    }
}
