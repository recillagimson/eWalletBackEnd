<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8"/>
    <title>SquidPay - Pay Bills Notification</title>
</head>

<body>
<h2>Hi {{ $firstName }}!</h2>
<p> Your payment of ₱{{ $amount }} to {{ $biller }} with fee ₱{{ $serviceFee }} has been successfully processed
    on {{ $transactionDate }} with ref. no {{ $refNo }}. Visit https://my.squid.ph/ for more information or contact
    support@squid.ph</p>
</body>

</html>
