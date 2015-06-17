<?php

namespace PaymentCommands\Paypal\Event;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class TransactionRedirect extends Event
{
    use SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

}
