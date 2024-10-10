<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Logged In</title>
</head>
<body>
    <h1>ログインしていません.</h1>
    <a href="{{ route('login', ['returnTo' => url('/profile?q=anyparams&test=params')]) }}">ログイン</a>
</body>
</html>
