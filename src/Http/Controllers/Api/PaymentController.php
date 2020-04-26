<?php

namespace Enzaime\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use EnzPayment;

class PaymentController extends Controller
{
    /**
     * Initiate payment
     *
     * @param Request $request
     * @return void
     */
    public function pay(Request $request)
    {
        $paymentInfoProvider = EnzPayment::paymentInfoProvider();

        $info = $paymentInfoProvider->getPaymentInfo($request);

        return EnzPayment::initiatePayment($info);
    }

    /**
     * Payment Success
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function success(Request $request)
    {
        return EnzPayment::responseHandler()->success($request->all());
    }

    /**
     * Payment Fail
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function fail(Request $request)
    {
        return EnzPayment::responseHandler()->fail($request->all());
    }
      
    /**
     * Payment Cancel
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function cancel(Request $request)
    {
        return EnzPayment::responseHandler()->cancel($request->all());
    }

    public function listenIpn(Request $request)
    {
        EnzPayment::getIpnListener()->listenIpn($request->all(), EnzPayment::getInstance());
    }

}
