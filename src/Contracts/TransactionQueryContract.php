<?php

namespace Enzaime\Payment\Contracts;

interface TransactionQueryContract
{
    /**
     * Fetch transaction by transaction's session-id, this should return the single transaction
     *
     * @param string $sessionId
     * @return array||mixed
     */
    public function getPaymentBySessionId(string $sessionId);

    /**
     * Fetch transaction(s) by transaction-id, this may return multiple transactions
     *
     * @param string $sessionId
     * @return void
     */
    public function getPaymentByTrnId(string $sessionId);
}