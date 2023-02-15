<?php

namespace App\Exception\Booking;

use Exception;

class BookingOverlapException extends Exception
{
    protected $message = 'booking overlap';

    protected $code = 2;
}
