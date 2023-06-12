<?php

declare(strict_types=1);

namespace App\Enum;

enum BookingStatusEnum: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PAID = 'paid';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case REFUND_REQUESTED = 'refund_requested';
    case REFUND = 'refund';
    case TERMINATED = 'terminated';
}
