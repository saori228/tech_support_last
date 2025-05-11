@extends('layouts.app')

@section('content')
    <div class="home-container">
        <h2 class="title">Главная страница</h2>
        
        <div class="button-container">
            @if(auth()->check())
                <a href="{{ route('profile') }}" class="button">Личный кабинет</a>
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.index') }}" class="button">Админ-панель</a>
                    <a href="{{ route('chat.index') }}" class="button">Чат с сотрудниками</a>
                @elseif(auth()->user()->isSupport())
                    <a href="{{ route('tickets.index') }}" class="button">История обращений</a>
                    <a href="{{ route('chat.index') }}" class="button">Чат с пользователями</a>
                @else
                    <a href="{{ route('tickets.index') }}" class="button">История обращений</a>
                    <a href="{{ route('tickets.create') }}" class="button">Создать обращение</a>
                    <a href="{{ route('chat.index') }}" class="button">Чат</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="button">Войти</a>
                <a href="{{ route('register') }}" class="button">Зарегистрироваться</a>
            @endif
        </div>
        
        @if(session('error'))
            <div class="alert alert-danger" style="margin-top: 20px; padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
                {{ session('error') }}
            </div>
        @endif
    </div>
    
    <style>
        .home-container {
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
        
        @media (max-width: 480px) {
            .home-container .title {
                font-size: 20px;
                margin-bottom: 15px;
            }
            
            .button-container .button {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
@endsection