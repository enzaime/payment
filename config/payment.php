<?php

return [
    'url_prefix' => 'api',
    'default' => [
        'gateway' => 'sslcommerz'
    ],
    
    'gateways' => [
        'sslcommerz' => [
            'url' => env('SSLZ_URL', 'https://securepay.sslcommerz.com'),
            'params' => [
                'store_id' => env('SSLZ_STORE_ID'),
                'store_passwd' => env('SSLZ_STORE_PASSWORD'),
                'success_url' => 'api/pay/success',
                'fail_url' => 'api/pay/fail',
                'cancel_url' => 'api/pay/cancel',
                'currency' => 'BDT',
                'emi_option' => '0',
            ],

            // Doc URL: https://developer.sslcommerz.com/doc/v4/#init-readyparams
            'required_params' => [

            ],

            'binds' => [
                'PaymentInfoContract' => '',
                'PaymentResponseContract' => '',
                'IpnListenerContract' => ''
            ]
        ]
    ]
];