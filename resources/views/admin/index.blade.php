@extends('layouts.app')

@section('content')
    <div class="admin-container">
        <h2 class="title">Админ-панель</h2>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="admin-users">
            <h3 class="admin-subtitle">Управление пользователями</h3>
            
            <div class="admin-users-list">
                @foreach($users as $user)
                    <div class="admin-user-item">
                        <div class="user-info">
                            <div class="user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                            <div class="user-email">{{ $user->email }}</div>
                            <div class="user-role">Роль: {{ $user->role->name }}</div>
                        </div>
                        <div class="user-actions">
                            <form action="{{ route('admin.users.role.update', $user) }}" method="POST" class="role-form">
                                @csrf
                                @method('PUT')
                                <select name="role_id" class="role-select">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="button">Изменить роль</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="admin-table-desktop">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Фамилия</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role->name }}</td>
                                <td>
                                    <form action="{{ route('admin.users.role.update', $user) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="role_id">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="button">Изменить роль</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="admin-links">
            <a href="{{ route('profile') }}" class="button">Личный кабинет</a>
            <a href="{{ route('home') }}" class="button">На главную</a>
            <a href="{{ route('chat.index') }}" class="button">Чат с сотрудниками</a>
        </div>
    </div>
    
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .admin-table th, .admin-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .admin-table th {
            background-color: #f2f2f2;
        }
        
        .admin-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .admin-table tr:hover {
            background-color: #f1f1f1;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        
        .admin-subtitle {
            margin: 20px 0 15px;
        }
        
        .admin-users-list {
            display: none;
        }
        
        .admin-user-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .user-info {
            margin-bottom: 10px;
        }
        
        .user-name {
            font-weight: bold;
            font-size: 16px;
        }
        
        .user-email, .user-role {
            font-size: 14px;
            margin-top: 5px;
        }
        
        .role-form {
            display: flex;
            flex-direction: column;
        }
        
        .role-select {
            margin-bottom: 10px;
            padding: 8px;
        }
        
        .admin-links {
            margin-top: 20px;
        }
        
        @media (max-width: 480px) {
            .admin-container .title {
                font-size: 20px;
                margin-bottom: 15px;
            }
            
            .admin-subtitle {
                font-size: 18px;
            }
            
            .admin-table-desktop {
                display: none;
            }
            
            .admin-users-list {
                display: block;
            }
            
            .admin-links {
                display: flex;
                flex-direction: column;
            }
            
            .admin-links .button {
                width: 100%;
            }
        }
    </style>
@endsection