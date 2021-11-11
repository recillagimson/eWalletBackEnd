<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8"/>
    <title>SquidPay - Send to Bank Transaction Receipt</title>
</head>

<body>
<h2>Here is your Send to Bank Transaction Receipt Details:</h2>
<table style="border: 0">
    <tr>
        <td>Amount Transferred:</td>
        <th>PHP {{ $amount }}</th>
    </tr>

    <tr>
        <td>Account Name:</td>
        <th>{{ $accountName }}</th>
    </tr>

    <tr>
        <td>Account Number:</td>
        <th>{{ $accountNumber }}</th>
    </tr>

    <tr>
        <td>Service Fee:</td>
        <th>{{ $serviceFee }}</th>
    </tr>

    <tr>
        <td>Transaction Date:</td>
        <th>{{ $transactionDate }}</th>
    </tr>

    <tr>
        <td>Reference No.:</td>
        <th>{{ $refNo }}</th>
    </tr>

    <tr>
        <td>Remittance ID:</td>
        <th>{{ $remittanceId }}</th>
    </tr>
</table>
</body>

</html>
