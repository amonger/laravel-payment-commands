<?php

namespace PaymentCommands\Paypal\Command;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;
use Omnipay\Common\GatewayInterface;
use PaymentCommands\Paypal\Event\TransactionFailed;
use PaymentCommands\Paypal\Event\TransactionRedirect;
use PaymentCommands\Paypal\Exception\TransactionFailedException;

class MakePayment extends Command implements SelfHandling
{
    protected $cancelUrl;
    protected $returnUrl;
    protected $description;
    protected $currency;
    protected $items;

    /**
     * @param array $items
     * @param string $currency
     * @param string $cancelUrl
     * @param string $returnUrl
     */
    public function __construct(array $items, $currency, $cancelUrl, $returnUrl)
    {
        $this->items = $items;
        $this->currency = $currency;
        $this->cancelUrl = $cancelUrl;
        $this->returnUrl = $returnUrl;
    }

    /**
     * @param GatewayInterface $gateway
     * @param Store $session
     * @param Dispatcher $dispatcher
     * @return mixed
     * @throws TransactionFailedException
     */
    public function handle(GatewayInterface $gateway, Store $session, Dispatcher $dispatcher)
    {
        $session->set('params', $this->details());
        $response = $gateway->purchase($this->details())->send();

        if ($response->isRedirect()) {
            // redirect to offsite payment gateway
            $dispatcher->fire(new TransactionRedirect($response->getMessage()));

            return $response->getRedirectUrl();
        } else {
            $dispatcher->fire(new TransactionFailed(
                $response->getMessage(),
                $response->getTransactionReference()
            ));

            throw new TransactionFailedException($response->getMessage());
        }
    }

    /**
     * @param array $items
     *
     * @return float
     */
    private function getSumTotal(array $items)
    {
        return array_reduce($items, function ($current, $item) {
            return $current + ($item['price'] * $item['quantity']);
        }, 0);
    }

    /**
     * @return array
     */
    private function details()
    {
        return [
            'amount' => $this->getSumTotal($this->items),
            'cancelUrl' => $this->cancelUrl,
            'returnUrl' => $this->returnUrl,
            'currency' => $this->currency,
            'items' => $this->items
        ];
    }
}