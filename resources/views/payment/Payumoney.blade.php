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
    'surl' => url('posts/create/payment'),
    'furl' => route('Payumoney.error'),
    'service_provider' => 'payu_paisa',
];
if (empty($posted['txnid'])) {
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
    $txnid = $posted['txnid'];
}

$hash = '';
$hashSequence = 'key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10';

if (empty($posted['hash']) && sizeof($posted) > 0) {
    $hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';
    foreach ($hashVarsSeq as $hash_var) {
        $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
        $hash_string .= '|';
    }
    $hash_string .= $SALT;

    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
} elseif (!empty($posted['hash'])) {
    $hash = $posted['hash'];
    $action = $PAYU_BASE_URL . '/_payment';
}
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
        <input type="hidden" name="amount" value="1000" /><br />
        <input type="hidden" name="firstname" id="firstname" value="<?= Auth::user()->name ?>" /><br />
        <input type="hidden" name="email" id="email" value="<?= Auth::user()->email ?>" /><br />
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
