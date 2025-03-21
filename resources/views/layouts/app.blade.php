<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}


    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100"> <!-- Правильная структура flexbox -->

    @include('layouts.header')

    <main class="container flex-grow-1 d-flex flex-column"> <!-- Добавил d-flex flex-column -->
        @yield('content')
    </main>

    @include('layouts.footer')

    <!-- Bootstrap Bundle с Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
