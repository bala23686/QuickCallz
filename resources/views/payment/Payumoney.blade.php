@php
$MERCHANT_KEY = env('MERCHANT_KEY');
$SALT = env('SALT');
$PAYU_BASE_URL = env('PAYU_TEST_MODE') ? 'https://test.payu.in' : 'https://secure.payu.in';
$AMOUNT = env('PAYU_TEST_MODE') ? 1000 : (int) $payment_info->amt;

$action = '';
$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
$posted = [];
$posted = [
    'key' => $MERCHANT_KEY,
    'txnid' => $txnid,
    'amount' => $AMOUNT,
    'firstname' => $payment_info->name,
    'email' => $payment_info->email,
    'productinfo' => 'PHP Project Subscribe',
    'surl' => route('Payumoney.success'),
    'furl' => route('Payumoney.error'),
    'service_provider' => 'payu_paisa',
];
if (empty($posted['txnid'])) {
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
    $txnid = $posted['txnid'];
}

$hash = '';
$hashSequence = "{$MERCHANT_KEY}|{$txnid}|{$AMOUNT}|{$posted['productinfo']}|{$payment_info->name}|{$payment_info->email}|||||||||||".$SALT;

if (empty($posted['hash']) && sizeof($posted) > 0) {
    $hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';
    foreach ($hashVarsSeq as $hash_var) {
        $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
        $hash_string .= '|';
    }
    // $hash_string .= ;

    $hash = strtolower(hash('sha512', $hashSequence));
    $action = $PAYU_BASE_URL . '/_payment';
} elseif (!empty($posted['hash'])) {
    $hash = $posted['hash'];
    $action = $PAYU_BASE_URL . '/_payment';
}

$hash = strtolower(hash('sha512', $hashSequence));
@endphp

<html>

<head>
    <script>
        var hash = '{{ $hash }}';

        function submitPayuForm() {
            if (hash == '') {
                return;
            }
            var payuForm = document.forms.payuForm;
            payuForm.submit();
        }
    </script>
</head>

<body onload="submitPayuForm()">
    <h4>Please Wait !! Processing.......</h4>
    <form action="{{ $action }}" method="post" name="payuForm"><br />
        <input type="hidden" name="key" value="{{ $MERCHANT_KEY }}" /><br />
        <input type="hidden" name="hash" value="{{ $hash }}" /><br />
        <input type="hidden" name="txnid" value="{{ $txnid }}" /><br />
        <input type="hidden" name="amount" value="{{$AMOUNT}}" /><br />
        <input type="hidden" name="firstname" id="firstname" value="{{$payment_info->name}}" /><br />
        <input type="hidden" name="email" id="email" value="{{ $payment_info->email }}" /><br />
        <input type="hidden" name="productinfo" value="PHP Project Subscribe"><br />
        <input type="hidden" name="surl" value="{{ url('posts/create/payment') }}" /><br />
        <input type="hidden" name="furl" value="{{ route('Payumoney.error') }}" /><br />
        <input type="hidden" name="service_provider" value="payu_paisa" /><br />

        @if (!$hash)
            <input type="submit" value="Submit" />
        @endif
    </form>
</body>

</html>
