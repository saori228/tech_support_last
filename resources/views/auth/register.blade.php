@extends('layouts.app')

@section('content')
    <div class="form-container">
        <div class="form-title">Регистрация</div>
        <div class="form-content">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" name="first_name" class="form-input" placeholder="Имя" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group">
                    <input type="text" name="last_name" class="form-input" placeholder="Фамилия" value="{{ old('last_name') }}" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Пароль" required>
                </div>
                <button type="submit" class="button">Зарегистрироваться</button>
                <a href="{{ route('login') }}" class="form-link">Уже есть аккаунт</a>
            </form>
        </div>
    </div>
@endsection
