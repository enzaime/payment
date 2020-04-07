<?php

namespace Enzaime\Payment;

use Illuminate\Support\Facades\Facade;

class PaymentFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return new PaymentManager();
    }
}