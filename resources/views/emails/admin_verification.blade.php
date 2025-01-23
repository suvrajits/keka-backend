<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Verification</title>
</head>
<body>
    <h2>Hello, {{ $admin->name }}</h2>
    <p>Your verification code is:</p>
    <h3 style="color: #2c3e50;">{{ $verification_code }}</h3>
    <p>Please enter this code to verify your account.</p>
</body>
</html>
