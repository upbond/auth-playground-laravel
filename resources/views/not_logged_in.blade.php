<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Logged In</title>
</head>
<body>
    <h1>You are not logged in.</h1>
    <a href="{{ route('login', ['returnTo' => url('/profile')]) }}">Log In</a>
</body>
</html>
