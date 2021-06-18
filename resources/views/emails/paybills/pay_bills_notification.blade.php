<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>SquidPay - Pay Bills Notification</title>
</head>

<body>
    <h2>Hi,</h2>
    <p>You have paid ₱{{ $amount }} of SquidPay on {{ date('Y-m-d H:i:s') }} from {{ $biller }}. Your new balance is ₱{{ $newBalance }} with Ref No. {{ $refNo }}. Thank you for using our Pay Bills service.</p>
</body>

</html>