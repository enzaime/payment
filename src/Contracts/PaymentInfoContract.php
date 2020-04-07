<?php

namespace Enzaime\Payment\Contracts;

use Illuminate\Http\Request;

interface PaymentInfoContract 
{
    /**
     * Return the payment, you use data from $request to retrieve order, customer, etc
     *
     * @param Request $request
     * @return array
     */
    public function getPaymentInfo(Request $request): array;
}