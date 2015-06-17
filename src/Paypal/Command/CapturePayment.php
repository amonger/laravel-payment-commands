<?php

namespace PaymentCommands\Paypal\Command;

use App\Events\Event;
use Illuminate\Console\Command;
use Dotenv;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Session;
use Omnipay\Common\GatewayInterface;
use Omnipay\PayPal\ExpressGateway;
use PaymentCommands\Paypal\Event\TransactionSuccessful;

class CapturePayment extends Command implements SelfHandling
{

    private $token;
    private $payerId;

    /**
     * @param string $token
     * @param string $payerId
     */
    public function __construct($token, $payerId)
    {
        $this->token = $token;
        $this->payerId = $payerId;
    }

    /**
     * @param GatewayInterface $gateway
     * @param Store $session
     * @param Dispatcher $dispatcher
     */
    public function handle(GatewayInterface $gateway, Store $session, Dispatcher $dispatcher)
    {
        $response = $gateway
            ->completePurchase($session->get('params'))
            ->setToken($this->token)
            ->setPayerId($this->payerId)
            ->send();

        $dispatcher->fire(new TransactionSuccessful($response->getData()));
    }
}
