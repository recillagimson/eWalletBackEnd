<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8"/>
    <title>SquidPay - Send Money Notification</title>
</head>

<body>
<h2>Hi {{ $receiverName }}!</h2>
<p>You have received ₱{{ $amount }} of SquidPay on {{ $transactionDate }} from {{ $senderName }}. Your new balance is
    ₱{{ $newBalance }} with Ref No. {{ $refNo }}. Use now to buy load, send money, pay bills and a lot more!</p>
</body>

</html>
