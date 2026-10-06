<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <title>NORELLE | Admin</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 40px; background: #fbf7f4;">
    <h1>Welcome to NORELLE Admin</h1>
    <p>Admin access is working. Orders will appear here next.</p>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Sign out</button>
    </form>
</body>
</html>