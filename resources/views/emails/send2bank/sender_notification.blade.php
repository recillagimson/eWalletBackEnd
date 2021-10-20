<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8"/>
    <title>SquidPay - Send to Bank Notification</title>
</head>

<body>
<h2>Hi {{ $firstName }}!</h2>
<p>
    You have sent P {{ $amount }} of SquidPay on {{ $transactionDate }} to the account ending in {{ $accountNo }}.
    Service Fee for this transaction is P {{ $serviceFee }}. Your new balance is P {{ $newBalance }} with SquidPay
    Ref. No. {{ $refNo }} & {{ $provider }} Remittance No. {{ $remittanceId }}. Thank you for using SquidPay!
</p>
</body>

</html>
