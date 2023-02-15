<?php

namespace App\Exception\SlotLock;

use Exception;

class LockConflictException extends Exception
{
    protected $message = 'slot already locked by another customer';

    protected $code = 2;
}
