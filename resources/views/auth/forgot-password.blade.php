@extends('layouts.app')

@section('content')
    <div class="form-container">
        <div class="form-title">Забыл пароль</div>
        <div class="form-content">
            <form action="{{ route('password.reset') }}" method="POST" class="auth-form">
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