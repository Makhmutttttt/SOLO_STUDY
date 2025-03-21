<!-- resources/views/layouts/header.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">My Tests Platform</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/tests') }}">My Tests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/tests/create') }}">Test AI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/tests/create-excel') }}">Test Excel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/tests/create-manual') }}">Test Manually</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/profile') }}">My Profile</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
