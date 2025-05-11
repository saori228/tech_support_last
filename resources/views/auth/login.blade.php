@extends('layouts.app')

@section('content')
    <div class="form-container">
        <div class="form-title">Авторизация</div>
        <div class="form-content">
            <form action="{{ route('login') }}" method="POST" class="auth-form">
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
    
    <style>
        .auth-form {
            width: 100%;
        }
        
        @media (max-width: 480px) {
            .form-container {
                width: 90%;
            }
            
            .form-title {
                font-size: 18px;
            }
            
            .form-group {
                margin-bottom: 12px;
            }
            
            .form-input {
                padding: 8px 15px;
            }
            
            .button {
                width: 100%;
            }
        }
    </style>
@endsection