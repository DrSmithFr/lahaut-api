<?php

namespace App\Service\Platform;

class PlatformFeeService
{
    public function computeFee(float $ca): float
    {
        // Fixed part
        $fee = 58.76;

        // Variable part (11.75%) for bookings > 10K€
        if ($ca > 10000.0) {
            $fee += $ca * 0.1175;
        }

        return $fee;
    }
}
