<?php

declare(strict_types=1);

namespace App\Enum;

enum SecurityRoleEnum: string
{
    case USER = 'ROLE_USER';
    case MONITOR = 'ROLE_MONITOR';
    case ADMIN = 'ROLE_ADMIN';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public function getRole(): string
    {
        return $this->value;
    }
}
