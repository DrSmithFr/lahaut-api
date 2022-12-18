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
            SecurityRoleEnum::USER => 'ROLE_USER',
            SecurityRoleEnum::MONITOR => 'ROLE_MONITOR',
            SecurityRoleEnum::ADMIN => 'ROLE_ADMIN',
            SecurityRoleEnum::SUPER_ADMIN => 'Super ROLE_SUPER_ADMIN',
        };
    }
}
