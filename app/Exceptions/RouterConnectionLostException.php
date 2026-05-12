<?php

namespace App\Exceptions;

use RuntimeException;

class RouterConnectionLostException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Connection to the router was lost mid-command.');
    }
}
