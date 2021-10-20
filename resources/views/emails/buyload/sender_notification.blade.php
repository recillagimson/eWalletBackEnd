<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8"/>
    <title>SquidPay - Buy Load Notification</title>
</head>

<body>
<h2>Hi {{ $firstName }}</h2>
<p>
    You have paid ₱ {{ $amount }} of SquidPay to purchase {{ $productName }} for {{ $recipientMobileNumber }}
    on {{ $transactionDate }}. Your SquidPay balance is ₱{{ $newBalance }}. Ref. No. {{ $refNo }}.
</p>
</body>

</html>
