<?php

declare(strict_types=1);

namespace App\Enum;

enum RoleEnum: string
{
    case CUSTOMER = 'ROLE_CUSTOMER';
    case MONITOR = 'ROLE_MONITOR';
    case ADMIN = 'ROLE_ADMIN';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
