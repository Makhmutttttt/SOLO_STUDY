<!-- resources/views/layouts/header.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Самообучение</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/tests') }}">Мои тесты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/tests/create') }}">AI тест</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/tests/input-issues') }}">Ошибки</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile') }}">Профиль</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
