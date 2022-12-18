<?php

declare(strict_types=1);

namespace App\Enum;

enum SecurityRoleEnum
{
    case USER;
    case MONITOR;
    case ADMIN;
    case SUPER_ADMIN;

    public function role(): string
    {
        return match ($this) {
            self::USER => 'ROLE_USER',
            self::MONITOR => 'ROLE_MONITOR',
            self::ADMIN => 'ROLE_ADMIN',
            self::SUPER_ADMIN => 'Super ROLE_SUPER_ADMIN',
        };
    }
}
