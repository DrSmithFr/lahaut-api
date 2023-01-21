<?php

declare(strict_types=1);

namespace App\Enum;

enum FlyTypeEnum: string
{
    case DISCOVERY = 'discovery';
    case FREESTYLE = 'freestyle';
    case KID = 'kid';
}
