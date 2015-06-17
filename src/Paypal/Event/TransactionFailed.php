<?php

namespace PaymentCommands\Paypal\Event;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class TransactionFailed extends Event
{
    use SerializesModels;

    public $message;
    public $transactionReference;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $transactionReference)
    {
        $this->message = $message;
        $this->transactionReference = $transactionReference;
    }
}
