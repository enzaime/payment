### Integration

Add the following repository to project's `composer.json`.

    "repositories": [
        {
            "type": "vcs",
            "url": "https://gitlab.com/enzaime/payment.git"
        },
        ....
    ],

After adding the following code  run `composer require enzaime/payment` command from your project terminal.

Add a class into your project that will return payment information to proceed transaction. This class must be implemented `Enzaime\Payment\Contracts\PaymentInfoContract` interface. You can visit [SslCommerz](https://developer.sslcommerz.com/doc/v4/#init-readyparams) to see the parameters which are required or not.

    <?php

    namespace App;

    use Enzaime\Payment\Contracts\PaymentInfoContract;
    use Illuminate\Http\Request;

    class PaymentInfoProvider implements PaymentInfoContract
    {
        public function getPaymentInfo(Request $request): array 
        {
            //Transaction Info
            $paymentInfo['total_amount'] = "50";
            $paymentInfo['tran_id'] = "5_".uniqid();
            $paymentInfo['product_category'] = "healthcare";

            # CUSTOMER INFORMATION
            $paymentInfo['cus_name'] = "Mahbbub";
            $paymentInfo['cus_email'] = "mahbub@gmail.com";
            $paymentInfo['cus_add1'] = "Dhaka";
            $paymentInfo['cus_add2'] = "Dhaka";
            $paymentInfo['cus_city'] = "Dhaka";
            $paymentInfo['cus_state'] = "Dhaka";
            $paymentInfo['cus_postcode'] = "1000";
            $paymentInfo['cus_country'] = "Bangladesh";
            $paymentInfo['cus_phone'] = '01710657367';
            // $paymentInfo['cus_fax'] = "";

            # SHIPMENT INFORMATION
            $paymentInfo['shipping_method'] = "NO";
            $paymentInfo['num_of_item'] = "1";
            $paymentInfo['ship_name'] = "Store Test";
            $paymentInfo['ship_add1 '] = "Dhaka";
            $paymentInfo['ship_add2'] = "Dhaka";
            $paymentInfo['ship_city'] = "Dhaka";
            $paymentInfo['ship_state'] = "Dhaka";
            $paymentInfo['ship_postcode'] = "1000";
            $paymentInfo['ship_country'] = "Bangladesh";

            # OPTIONAL PARAMETERS
            $paymentInfo['value_a'] = "ref001";
            $paymentInfo['value_b '] = "ref002";
            $paymentInfo['value_c'] = "ref003";
            $paymentInfo['value_d'] = "ref004";

            # EMI STATUS
            $paymentInfo['emi_option'] = "0";

            //PRODUCT INFO
            $paymentInfo['product_name'] = 'Consultation';
            $paymentInfo['product_category'] = 'HealthCare';
            $paymentInfo['product_profile'] = 'general';

            # CART PARAMETERS
            $paymentInfo['cart'] = json_encode(array(
                array("product"=>"DHK TO BRS AC A1","amount"=>"200.00"),
                array("product"=>"DHK TO BRS AC A2","amount"=>"200.00"),
                array("product"=>"DHK TO BRS AC A3","amount"=>"200.00"),
                array("product"=>"DHK TO BRS AC A4","amount"=>"200.00")
            ));
            $paymentInfo['product_amount'] = "100";
            $paymentInfo['vat'] = "5";
            $paymentInfo['discount_amount'] = "5";
            $paymentInfo['convenience_fee'] = "3";

            return $paymentInfo;
        }
    }

Now, Add another class into your project that will handle the response of the payment request like *success, fail, cancel*. This class must be implemented `Enzaime\Payment\Contracts\PaymentResponseContract` interface.

    <?php

    namespace App;

    use Enzaime\Payment\Contracts\PaymentResponseContract;

    class PaymentResponseHandler implements PaymentResponseContract
    {
        public function success(array $payload)
        {
            //update your order 

            //Redirect or return data for successful payment
            return redirect('success-url');
        }

        public function fail(array $payload)
        {
            //update your order 

            //Redirect or return data for unsuccessful payment
            return redirect('fail-url');
        }

        public function cancel(array $payload)
        {
            //update your order 

            //Redirect or return data for payment cancellation
            return redirect('cancel-url');
        }
    }

### Configuration

Run following command to publish the configuration and views.

    php artisan vendor:publish --provider="Enzaime\Payment\ServiceProvider"

Now set the implemented classes of `Enzaime\Payment\Contracts\PaymentInfoContract` and `Enzaime\Payment\Contracts\PaymentResponseContract` contracts into `config/payment.php`;

    'binds' => [
        'PaymentInfoContract' => App\PaymentInfoProvider::class,
        'PaymentResponseContract' =>App\PaymentResponseHandler::class
    ]`

Modify the `.env` file to set the SslCommerz `store_id` and `store_passwd`;

    SSLZ_URL='https://sandbox.sslcommerz.com'
    SSLZ_STORE_ID=your-store-id
    SSLZ_STORE_PASSWORD=your-store-password


> For production set `SSLZ_URL='https://securepay.sslcommerz.com'`

### Add Payment Button

Include the following views into your `view` file. You are free to modify the button except the `id` attribute.

    <!---Pay button>
    @include('vendor.enzaime-payment.sslcommerz.button')

    <!--- Dev script>
    @include('vendor.enzaime-payment.sslcommerz.dev-script')

> For production use `@include('vendor.enzaime-payment.sslcommerz.dev-script')`


### Available Methods

- `EnzPayment::initiatePayment($info)`
- `EnzPayment::getPaymentBySessionId(string $sessionId)`
- `EnzPayment::getPaymentByTrnId(string $trnId)`
- `EnzPayment::requestForRefund($bankTrnId, $refundAmount, $refundRemarks, $othersData = [])`
- `EnzPayment::getRefundRequest(string $refId)`

### Test Payment

To get card info for mock transaction visit [SsslCommerz](https://developer.sslcommerz.com/doc/v4/#init-readyparams)