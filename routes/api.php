<?php

Route::group([
    'middleware' => 'api',
    'prefix' => 'api',
    'namespace' => 'Enzaime\Payment\Http\Controllers\Api'
],  function() {
    Route::any('pay', 'PaymentController@pay');

    Route::post('pay/success', 'PaymentController@success')->name('pay.success');
    Route::post('pay/fail', 'PaymentController@fail')->name('pay.fail');
    Route::post('pay/cancel', 'PaymentController@cancel')->name('pay.cancel');
});