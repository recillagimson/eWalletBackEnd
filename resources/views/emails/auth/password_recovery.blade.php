<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8"/>
    <title>SquidPay - Account {{ ucwords($pinOrPassword) }} Recovery Verification</title>
</head>
<body>
<h2>Hi {{ $recipientName }}</h2>
<p>This is your {{ $pinOrPassword }} verification code: {{ $code }} . PLEASE DO NOT SHARE YOUR OTP.</p>
</body>
</html>
