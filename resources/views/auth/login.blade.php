@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg p-4" style="width: 350px;">
        <h2 class="text-center mb-3">Вход</h2>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Введите email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Введите пароль" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('register.form') }}">Нет аккаунта? Зарегистрируйтесь</a>
        </div>
    </div>
</div>
@endsection
