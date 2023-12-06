<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Admin Password</title>
</head>
<body>
    <p>Click on the link or copy and paste the URL below to reset your administrative password</p>
    <br>
    <a href="https://fbn-admin-wheat.vercel.app?forgot-password={{ $admin->reset_password_verification }}">https://fbn-admin-wheat.vercel.app?forgot-password={{ $admin->reset_password_verification }}</a>
</body>
</html>