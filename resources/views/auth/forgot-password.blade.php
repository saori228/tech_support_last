@extends('layouts.app')

@section('content')
    <div class="form-container">
        <div class="form-title">Забыл пароль</div>
        <div class="form-content">
            <form action="{{ route('password.reset') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Новый пароль" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Подтвердить пароль" required>
                </div>
                <button type="submit" class="button">Изменить</button>
                <a href="{{ route('login') }}" class="form-link">Я помню пароль</a>
            </form>
        </div>
    </div>
@endsection
