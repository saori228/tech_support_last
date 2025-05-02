@extends('layouts.app')

@section('content')
    <div class="form-container">
        <div class="form-title">Авторизация</div>
        <div class="form-content">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Пароль" required>
                </div>
                <a href="{{ route('password.request') }}" class="form-link">Забыл пароль</a>
                <button type="submit" class="button">Войти</button>
                <a href="{{ route('register') }}" class="form-link">Создать аккаунт</a>
            </form>
        </div>
    </div>
@endsection
