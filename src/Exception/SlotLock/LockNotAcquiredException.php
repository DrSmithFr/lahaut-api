<?php

namespace App\Exception\SlotLock;

use Exception;

class LockNotAcquiredException extends Exception
{
    protected $message = 'lock is not acquired';

    protected $code = 2;
}
