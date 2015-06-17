<?php

namespace PaymentCommands\Paypal\Providers;

use Dotenv;
use Illuminate\Support\ServiceProvider;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Omnipay\PayPal\ExpressGateway;

class GatewayServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function register()
    {
        $gateway = Omnipay::create('PayPal_Express');

        $this->app->bind(GatewayInterface::class, function () use ($gateway) {
            /** @var \Omnipay\PayPal\ExpressGateway $gateway */
            $gateway->setUsername(Dotenv::findEnvironmentVariable('PAYPAL_USERNAME'));
            $gateway->setPassword(Dotenv::findEnvironmentVariable('PAYPAL_PASSWORD'));
            $gateway->setSignature(Dotenv::findEnvironmentVariable('PAYPAL_SIGNATURE'));

            if (Dotenv::findEnvironmentVariable('PAYPAL_TEST_MODE') === "true") {
                $gateway->setTestMode(true);
            }

            return $gateway;
        });
    }
}
