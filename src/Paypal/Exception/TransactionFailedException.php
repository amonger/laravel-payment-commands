<?php

namespace PaymentCommands\Paypal\Exception;

use Exception;

class TransactionFailedException extends Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
