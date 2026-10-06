<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Sign In | NORELLE</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body class="admin-login">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="login-card bg-white shadow-sm w-100">
            <div class="text-center mb-4">
                <div class="login-brand">NORELLE</div>
                <p class="login-eyebrow mb-2">STORE MANAGEMENT</p>
                <h1 class="login-title mb-2">Welcome back</h1>
                <p class="text-secondary small mb-0">
                    Sign in to manage your store and orders.
                </p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger small" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ url('/admin/login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn login-button w-100 py-3">
                    Sign in
                </button>
            </form>

            <p class="login-footnote text-center small mb-0 mt-4">
                NORELLE Store Management
            </p>
        </div>
    </main>
</body>
</html>