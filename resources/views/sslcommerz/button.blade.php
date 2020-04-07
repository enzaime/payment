<!-- https://developer.sslcommerz.com/doc/v4/#easy-chechout -->
<button class="my-btn" id="sslczPayBtn"
    token="{{$token ?? csrf_token()}}"
    postdata="{{$postData ?? ''}}"
    order="100"
    endpoint="{{ $url ?? url('api/pay') }}">
    {{ $slote ?? 'Pay Now'}}
</button>