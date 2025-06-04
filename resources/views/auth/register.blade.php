@extends('layouts.app')

@section('content')
    <div class="form-container">
        <div class="form-title">Регистрация</div>
        <div class="form-content">
            <form action="{{ route('register') }}" method="POST" class="auth-form">
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
                <button type="submit" class="button_rega">Зарегистрироваться</button>
                <a href="{{ route('login') }}" class="form-link">Уже есть аккаунт</a>
            </form>
        </div>
    </div>
    
    <style>
        .auth-form {
            width: 100%;
        }
        .button_rega{
            display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            margin: 10px 0px 10px 110px;
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
            
            .button_rega {
                  display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            margin: 10px 0px 10px 65px;
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