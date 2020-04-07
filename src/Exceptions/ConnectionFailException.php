<?php

namespace Enzaime\Payment\Exceptions;

use Exception;

class ConnectionFailException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string|null  $message
     * @return void
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: json_last_error());
    }
}