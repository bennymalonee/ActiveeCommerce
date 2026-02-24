<script src="//pay.voguepay.com/js/voguepay.js"></script>

<script>
<<<<<<< HEAD
    closedFunction = function () {
        location.href = '{{ env('APP_URL') }}'
    }

    successFunction = function (transaction_id) {
        location.href = '{{ env('APP_URL') }}' + '/vogue-pay/success/' + transaction_id
    }
    failedFunction = function (transaction_id) {
        location.href = '{{ env('APP_URL') }}' + '/vogue-pay/failure/' + transaction_id
=======
    closedFunction=function() {
        location.href = '{{ env('APP_URL') }}'
    }

    successFunction=function(transaction_id) {
        location.href = '{{ env('APP_URL') }}'+'/vogue-pay/success/'+transaction_id
    }
    failedFunction=function(transaction_id) {
        location.href = '{{ env('APP_URL') }}'+'/vogue-pay/failure/'+transaction_id
>>>>>>> 69a9cd1a8fe75090888a01f1747deb3f74fcaa01
    }
</script>
@if (get_setting('voguepay_sandbox') == 1)
    <input type="hidden" id="merchant_id" name="v_merchant_id" value="demo">
@else
    <input type="hidden" id="merchant_id" name="v_merchant_id" value="{{ env('VOGUE_MERCHANT_ID') }}">
@endif
@php
<<<<<<< HEAD
    $order = get_order_info(Session::get('payment_data')['order_id']);
=======
    $order = get_order_info(Session::get('payment_data')['order_id']);s
>>>>>>> 69a9cd1a8fe75090888a01f1747deb3f74fcaa01
    $shipping_info = json_decode($order->shipping_address);
@endphp

<script>

<<<<<<< HEAD
    window.onload = function () {
        pay3();
    }

    function pay3() {
        Voguepay.init({
            v_merchant_id: document.getElementById("merchant_id").value,
            total: '{{ $order->grand_total }}',
            cur: '{{ get_system_default_currency()->code }}',
            merchant_ref: 'ref123',
            memo: 'Payment for shirt',
            developer_code: '5a61be72ab323',
            store_id: 1,
            loadText: 'Custom load text',

            customer: {
=======
        window.onload = function(){
            pay3();
        }

        function pay3() {
         Voguepay.init({
             v_merchant_id: document.getElementById("merchant_id").value,
             total: '{{ $order->grand_total }}',
             cur: '{{ get_system_default_currency()->code }}',
             merchant_ref: 'ref123',
             memo: 'Payment for shirt',
             developer_code: '5a61be72ab323',
             store_id: 1,
             loadText:'Custom load text',

             customer: {
>>>>>>> 69a9cd1a8fe75090888a01f1747deb3f74fcaa01
                name: '{{ $shipping_info->name }}',
                address: '{{ $shipping_info->address }}',
                city: '{{ $shipping_info->city }}',
                state: 'Customer state',
                zipcode: '{{ $shipping_info->postal_code }}',
                email: '{{ $shipping_info->email }}',
                phone: '{{ $shipping_info->phone }}'
            },
<<<<<<< HEAD
            closed: closedFunction,
            success: successFunction,
            failed: failedFunction
        });
    }
</script>
=======
             closed:closedFunction,
             success:successFunction,
             failed:failedFunction
         });
        }
</script>
>>>>>>> 69a9cd1a8fe75090888a01f1747deb3f74fcaa01
