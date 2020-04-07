<?php

namespace Enzaime\Payment\Contracts;

interface PaymentResponseContract
{
    /**
     * Process your order with payload after completing payment and you may redirect user to other route 
     *
     * @param array $payload
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function success(array $payload);

    /**
     * Process your order with payload after failing payment and you may redirect user to other route 
     *
     * @param array $payload
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function fail(array $payload);

    /**
     * Process your order with payload after cancelling payment and you may redirect user to other route 
     *
     * @param array $payload
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function cancel(array $payload);
}