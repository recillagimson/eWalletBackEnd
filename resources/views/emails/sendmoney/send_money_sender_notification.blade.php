<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8"/>
    <title>SquidPay - Send Money Notification</title>
</head>

<body>
<h2>Hi {{ $senderName  }}!</h2>
<p>You have sent ₱{{ $amount }} of SquidPay on {{ $transactionDate }} to {{ $receiverName }}. Convenience fee for this
    transaction is ₱{{ $serviceFee }}. Your new balance is ₱{{ $newBalance }} with Ref No. {{ $refNo }}. Thank you for
    using SquidPay!</p>
</body>

</html>
