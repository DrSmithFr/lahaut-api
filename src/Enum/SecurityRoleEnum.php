<?php

declare(strict_types = 1);

namespace App\Enum;

final class SecurityRoleEnum extends Enum
{
    public const USER        = 'ROLE_USER';
    public const ADMIN       = 'ROLE_ADMIN';
    public const SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
