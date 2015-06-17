<?php

namespace PaymentCommands\Paypal\Event;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class TransactionSuccessful extends Event
{
    use SerializesModels;

    public $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }
}
