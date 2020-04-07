<?php

namespace Enzaime\Payment;

use Enzaime\Payment\Contracts\PaymentContract;
use Enzaime\Payment\Gateways\SslCommerz;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class PaymentManager
{
    private $gateways = [];

    private $name = null;

    /**
     * Return the payment gateway
     *
     * @param string $name
     * @return \Enzaime\Payment\Contracts\PaymentContract
     */
    public function gateway($name = null)
    {
        $name = $name ?: $this->getDefaultGateway();

        $this->setGateway($name);

        return $this->gateways[$name] ?? $this->gateways[$name] = $this->resolve($name);
    }

    /**
     * Set gateway name 
     *
     * @param string|null $name
     * @return void
     */
    public function setGateway(?string $name)
    {
        $this->name = $name;
    }

    /**
     * return Gateway name
     *
     * @return string|null
     */
    public function getGateway(): ?string
    {
        return $this->name;
    }

    /**
     * Resolve the given guard.
     *
     * @param  string  $name
     * @return \Enzaime\Payment\Contracts\PaymentContract;
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Auth gateway[{$name}] is not defined.");
        }

        $driverMethod = 'create'.ucfirst($name).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }

        throw new InvalidArgumentException(
            "Auth driver [{$name}] for gateway[{$name}] is not defined."
        );
    }

    /**
     * Get the gateway configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return config("payment.gateways.{$name}");
    }

    public function getDefaultGateway()
    {
        return config('payment.default.gateway');
    }

    /**
     * Undocumented function
     *
     * @return PaymentContract;
     */
    public function createSslCommerzDriver()
    {
        return new SslCommerz();
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->gateway()->{$method}(...$parameters);
    }
}