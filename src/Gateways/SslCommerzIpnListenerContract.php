<?php

namespace Enzaime\Payment\Gateways;

interface SslCommerzIpnListenerContract
{
    /**
     * Listen payment validation response and update your transaction accordingly
     * 
     * @param array $paymentInfo
     * @return string|array|mixed
     */
    public function listenIpn(array $paymentInfo, SslCommerzIpnValidatorContract $validator);
}