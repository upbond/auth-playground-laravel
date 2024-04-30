<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged In</title>
</head>
<body>
    <h1>Hello, {{ $name }}!</h1>
    <p>Your email address is {{ $email }}.</p>
    <!-- Your HTML content for logged in user -->
    <form action="/logout" method="GET">
        <button type="submit">Logout</button>
    </form>

    <p>Your Token: </p>
    <span>{{ $token }}</span>
    <br>
    <br>

    <div>
        <pre>{{ json_encode($user, JSON_PRETTY_PRINT) }}</pre>
    </div>
</body>
</html>
