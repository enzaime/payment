<?php

namespace Enzaime\Payment\Contracts;

interface PaymentContract
{
    /**
     * Send the request to service-provide with payment info
     * 
     * @param array $paymentInfo
     * @return string|array|mixed
     */
    public function initiatePayment(array $paymentInfo);
}