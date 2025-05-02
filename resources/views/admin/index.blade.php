@extends('layouts.app')

@section('content')
    <div class="admin-container">
        <div class="admin-title">Админка</div>
        <div class="admin-content">
            <div style="margin-bottom: 20px; font-weight: bold;">Управление ролями пользователей</div>
            
            @foreach($users as $user)
                <div class="user-item">
                    <div class="user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                    <div class="user-role">
                        <form action="{{ route('admin.users.role.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="role_id" onchange="this.form.submit()">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        <div class="role-toggle">✓</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

