<?php

namespace Enzaime\Payment\Gateways;

interface SslCommerzIpnValidatorContract
{
    /**
     * Send the request to get validation response
     * 
     * @param string $validationId
     * @return string|array|mixed
     */
    public function validateIpn(string $validationId);
}