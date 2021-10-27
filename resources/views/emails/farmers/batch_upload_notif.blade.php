<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8"/>
    <title>{{ $subject }}</title>
</head>

<body>
<h2>Hi {{ $firstName }}!</h2>
<p>
    Farmers Information Uploaded. Click the link below for the result: <br/>
    <b>Uploaded Records:</b> <a href="{{ $successLink }}">Click Here</a> <br/>
    <b>Failed Records:</b> <a href="{{ $failedLink }}">Click Here</a>
</p>
</body>

</html>
