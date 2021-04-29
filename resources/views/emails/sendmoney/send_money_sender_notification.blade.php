<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>SquidPay - Send Money Notification</title>
</head>

<body>
    <h2>Hi,</h2>
    <p>You have sent ₱{{ $amount }} of SquidPay on {{ date('Y-m-d H:i:s') }} to {{ $receiverName }}. Convenience fee for this transaction is ₱{{ $serviceFee }}. Your new balance is ₱{{ $newBalance }} with Ref No. {{ $refNo }}. Thank you for using SquidPay!</p>
</body>

</html>