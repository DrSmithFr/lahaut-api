<?php

namespace App\Exception\SlotLock;

use Exception;

class LockExpiredException extends Exception
{
    protected $message = 'lock expired';

    protected $code = 2;
}
