<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8"/>
    <title>SquidPay - Admin Account Details</title>
</head>

<body>
<h2>Hi {{ $firstName }},</h2>
<p>
    An admin account has been created using your email. Please see details below: <br/>
    <b>Email:</b> {{ $email }} <br/>
    <b>Password:</b> {{ $password }}
</p>
</body>

</html>
