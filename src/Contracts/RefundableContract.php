<?php

namespace Enzaime\Payment\Contracts;

interface RefundableContract
{
    /**
     * Raise the refund request
     *
     * @param string $bankTrnId
     * @param string $refundAmount
     * @param string $refundRemarks
     * @param array $othersData
     * @return array|mixed
     */
    public function requestForRefund($bankTrnId, $refundAmount, $refundRemarks, $othersData = []);

    /**
     * Query Refund request
     *
     * @param string $refId
     * @return array|mixed
     */
    public function getRefundRequest(string $refId);
}