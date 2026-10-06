@props([
    'title' => 'Dashboard',
    'bodyClass' => 'admin-products',
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }} | NORELLE Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">

    @stack('styles')
</head>

<body class="{{ $bodyClass }}">
    <header class="bg-white border-bottom">
        <div class="container d-flex align-items-center justify-content-between py-3">
            <a
                href="{{ route('admin.dashboard') }}"
                class="admin-products-brand"
            >
                NORELLE <small>ADMIN</small>
            </a>

            @include('admin.partials.navigation')
        </div>
    </header>

    {{ $slot }}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const successAlert = document.getElementById('successAlert');

        if (successAlert) {
            setTimeout(() => {
                bootstrap.Alert.getOrCreateInstance(successAlert).close();
            }, 5000);
        }
    </script>

    @stack('scripts')
</body>
</html>