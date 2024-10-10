<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <h1>プロフィル</h1>
    
    <h2>AccessToken内容</h2>
    <pre>{{ json_encode($decodedToken, JSON_PRETTY_PRINT) }}</pre>

    <a href="{{ route('logout') }}">ログアウト</a>
</body>
</html>
