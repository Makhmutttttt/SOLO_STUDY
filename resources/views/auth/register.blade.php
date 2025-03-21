@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg p-4" style="width: 350px;">
        <h2 class="text-center mb-3">Регистрация</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Введите имя" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Введите email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Введите пароль" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Повторите пароль" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Зарегистрироваться</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login.form') }}">Уже есть аккаунт? Войти</a>
        </div>
    </div>
</div>
@endsection
