<?php

namespace Enzaime\Payment\Gateways;

use Enzaime\Payment\Contracts\PaymentContract;
use Enzaime\Payment\Contracts\PaymentInfoContract;
use Enzaime\Payment\Contracts\PaymentResponseContract;
use Enzaime\Payment\Contracts\TransactionQueryContract;
use Enzaime\Payment\Exceptions\ConnectionFailExcept;
use Enzaime\Payment\Exceptions\ConnectionFailException;
use Enzaime\Payment\Exceptions\InvalidPaymentParamException;
use Illuminate\Queue\InvalidPayloadException;
use Illuminate\Support\Arr;

class SslCommerz implements PaymentContract, TransactionQueryContract
{
    /**
     * Validate payment .
     *
     * @param  string  $name
     * @return \Enzaime\Payment\Contracts\PaymentContract;
     *
     * @throws \Enzaime\Payment\Exceptions\InvalidPaymentParamException
     */
    public function validatePaymentInfo(array $paymentInfo)
    {
        $requiredParams = config('payment.gateways.sslcommerz.required_params', []);

        foreach ($requiredParams as $param) {
            if (!isset($paymentInfo[$param])) {
                throw new InvalidPaymentParamException("Parameter [{$param}] is requird.");
            }
        }

        return true;
    }

    /**
     * Send the request to service-provide with payment info
     * Doc URL: https://developer.sslcommerz.com/doc/v4/#init-createsession
     * 
     * @return string|array|mixed
     */
    public function initiatePayment(array $paymentInfo)
    {
        $pData = $this->getConfigParams();

        $pData = array_merge($pData, $paymentInfo);

        $pData['success_url'] = url($pData['success_url']);
        $pData['fail_url'] = url($pData['fail_url']);
        $pData['cancel_url'] = url($pData['cancel_url']);

        $this->validatePaymentInfo($pData);

        $params = $pData;

        $url = "{$this->getUrl()}/gwprocess/v4/api.php";

        $sslcz = json_decode($this->sendRequest($url, $params), true);

        if (isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] != "") {
            // this is important to show the popup, return or echo to sent json response back
            return  json_encode(['status' => 'success', 'data' => $sslcz['GatewayPageURL'], 'logo' => $sslcz['storeLogo'] ]);
        } else {
            return  json_encode(['status' => 'fail', 'data' => null, 'message' => "JSON Data parsing error!"]);
        }
    }

    /**
     * Send request to the SsslCommerz server and return response
     *
     * @param string $url
     * @param array $params
     * @param string $method
     * @return mixed
     */
    private function sendRequest(string $url, array $params, $method = 'POST')
    {
        $handle = curl_init();

        if ($method == 'POST') {
            curl_setopt($handle, CURLOPT_POST, 1 );
            curl_setopt($handle, CURLOPT_POSTFIELDS, $params);
        } else {
            $url = $url . '?' . http_build_query($params);
        }

        curl_setopt($handle, CURLOPT_URL, $url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC

        $content = curl_exec($handle );

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            return  $content;
        } else {
            curl_close( $handle);
            $msg = "FAILED TO CONNECT WITH SSLCOMMERZ API";

            throw new ConnectionFailException($msg);
        }
    }

    /**
     * Undocumented function
     *
     * @return PaymentResponseContract
     */
    public function responseHandler(): PaymentResponseContract
    {
        $className = config('payment.gateways.sslcommerz.binds.PaymentResponseContract');
        
        return app()->make($className);
    }

    /**
     * Undocumented function
     *
     * @return PaymentInfoContract
     */
    public function paymentInfoProvider(): PaymentInfoContract
    {
        $className = config('payment.gateways.sslcommerz.binds.PaymentInfoContract');
        
        return app()->make($className);
    }

    /**
     * Return url of SslCommerz
     *
     * @return string
     */
    private function getUrl(): string
    {
        return config('payment.gateways.sslcommerz.url');
    }

    /**
     * Fetch transaction by transaction's session-id, this should return the single transaction
     * Doc URL: https://developer.sslcommerz.com/doc/v4/#query-by-session-id
     * 
     * @param string $sessionId
     * @return array||mixed
     */
    public function getPaymentBySessionId(string $sessionId)
    {
        $params =  Arr::only($this->getConfigParams(), ['store_id', 'store_passwd']);
        
        $params['sessionkey'] = $sessionId;

        $url = $this->getUrl() . '/validator/api/merchantTransIDvalidationAPI.php';

        return json_decode($this->sendRequest($url, $params, 'GET'), true);
    }

    /**
     * Fetch transaction(s) by transaction-id, this may return multiple transactions
     * Doc URL: https://developer.sslcommerz.com/doc/v4/#query-by-transaction-id
     * 
     * @param string $trnId
     * @return void
     */
    public function getPaymentByTrnId(string $trnId)
    {
        $params =  Arr::only($this->getConfigParams(), ['store_id', 'store_passwd']);
        
        $params['tran_id'] = $trnId;

        $url = $this->getUrl() . '/validator/api/merchantTransIDvalidationAPI.php';

        return json_decode($this->sendRequest($url, $params, 'GET'), true);
    }

    /**
     * Raise the refund request
     * Doc URL: https://developer.sslcommerz.com/doc/v4/#initiate-refund-section
     *
     * @param string $bankTrnId
     * @param string $refundAmount
     * @param string $refundRemarks
     * @param array $othersData
     * @return array|mixed
     */
    public function requestForRefund($bankTrnId, $refundAmount, $refundRemarks, $othersData = [])
    {
        $params =  Arr::only($this->getConfigParams(), ['store_id', 'store_passwd']);

        $params['bank_tran_id'] = $bankTrnId;
        $params['refund_amount'] = $refundAmount;
        $params['refund_remarks'] = $refundRemarks;

        $url = $this->getUrl() . '/validator/api/merchantTransIDvalidationAPI.php';

        return json_decode($this->sendRequest($url, $params, 'GET'), true);
    }

     /**
     * Query Refund request
     * Doc URL: https://developer.sslcommerz.com/doc/v4/#query-refund-status
     * 
     * @param string $refId
     * @return array|mixed
     */
    public function getRefundRequest(string $refId)
    {
        $params =  Arr::only($this->getConfigParams(), ['store_id', 'store_passwd']);
        $params['refund_ref_id'] = $refId;

        $url = $this->getUrl() . '/validator/api/merchantTransIDvalidationAPI.php';

        return json_decode($this->sendRequest($url, $params, 'GET'), true);
    }

    private function getConfigParams()
    {
        return config('payment.gateways.sslcommerz.params');
    }
}