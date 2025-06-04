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
                <button type="submit" class="button_voiti">Войти</button>
                <a href="{{ route('register') }}" class="form-link">Создать аккаунт</a>
            </form>
        </div>
    </div>
    
    <style>
        .auth-form {
            width: 100%;
        }
        .button_voiti{
            display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            margin: 10px 0px 10px 120px;
            text-align: center;
            min-width: 200px;
            cursor: pointer;
            border: none;
            font-size: 16px;
            font-weight: 900;
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
            
            .button_voiti {
                  display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            margin: 10px 0px 10px 70px;
            text-align: center;
            min-width: 200px;
            cursor: pointer;
            border: none;
            font-size: 16px;
            font-weight: 900;
            }
        }
    </style>
@endsection