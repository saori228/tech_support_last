@extends('layouts.app')

@section('content')
    <h2 class="title">Личный кабинет</h2>
    
    <div class="profile-container">
        <div class="profile-left">
            <div class="profile-title">Личные данные:</div>
            
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="profile-field">
                    <div>{{ $user->first_name }}:</div>
                    <div>
                        <input type="text" name="first_name" value="{{ $user->first_name }}" style="display: none;" id="first_name_input">
                        <button type="button" onclick="toggleEdit('first_name')" id="first_name_btn">Изменить</button>
                    </div>
                </div>
                
                <div class="profile-field">
                    <div>{{ $user->last_name }}:</div>
                    <div>
                        <input type="text" name="last_name" value="{{ $user->last_name }}" style="display: none;" id="last_name_input">
                        <button type="button" onclick="toggleEdit('last_name')" id="last_name_btn">Изменить</button>
                    </div>
                </div>
                
                <div class="profile-field">
                    <div>********:</div>
                    <div>
                        <input type="password" name="password" style="display: none;" id="password_input">
                        <button type="button" onclick="toggleEdit('password')" id="password_btn">Изменить</button>
                    </div>
                </div>
                
                <div class="profile-field">
                    <div>{{ $user->email }}:</div>
                    <div>
                        <input type="email" name="email" value="{{ $user->email }}" style="display: none;" id="email_input">
                        <button type="button" onclick="toggleEdit('email')" id="email_btn">Изменить</button>
                    </div>
                </div>
                
                <button type="submit" class="button" id="submit_btn" style="display: none;">Подтвердить изменения</button>
            </form>
        </div>
        
        <div class="profile-right">
            <a href="{{ route('tickets.index') }}" class="button">История обращений</a>
            <a href="{{ route('tickets.create') }}" class="button">Создать обращение</a>
            <a href="{{ route('chat.index') }}" class="button">Чат</a>
            
            @if($user->isAdmin())
                <a href="{{ route('admin.index') }}" class="button">Админка</a>
            @endif
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="button">Выйти</button>
            </form>
        </div>
    </div>
    
    <script>
        function toggleEdit(field) {
            const input = document.getElementById(field + '_input');
            const btn = document.getElementById(field + '_btn');
            const submitBtn = document.getElementById('submit_btn');
            
            if (input.style.display === 'none') {
                input.style.display = 'inline-block';
                btn.textContent = 'Отмена';
                submitBtn.style.display = 'block';
            } else {
                input.style.display = 'none';
                btn.textContent = 'Изменить';
                
                // Проверяем, есть ли еще видимые поля ввода
                const inputs = document.querySelectorAll('input[id$="_input"]');
                let allHidden = true;
                
                for (const inp of inputs) {
                    if (inp.style.display !== 'none') {
                        allHidden = false;
                        break;
                    }
                }
                
                if (allHidden) {
                    submitBtn.style.display = 'none';
                }
            }
        }
    </script>
@endsection
