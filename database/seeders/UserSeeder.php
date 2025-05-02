<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем ID ролей
        $adminRole = Role::where('name', 'admin')->first()->id;
        $supportRole = Role::where('name', 'сотрудник')->first()->id;
        $userRole = Role::where('name', 'пользователь')->first()->id;

        // Создаем администратора
        User::create([
            'first_name' => 'Админ',
            'last_name' => 'Системы',
            'email' => 'sasha123no@gmail.com',
            'password' => Hash::make('12345678A'),
            'role_id' => $adminRole,
        ]);

        // Создаем сотрудника поддержки
        User::create([
            'first_name' => 'Александр',
            'last_name' => 'Коротчук',
            'email' => 'korotchuk8673@gmail.com',
            'password' => Hash::make('851293271SID'),
            'role_id' => $supportRole,
        ]);

        // Создаем пользователей
        User::create([
            'first_name' => 'Никита',
            'last_name' => 'Курашов',
            'email' => 'kurashovnikita8517@gmail.com',
            'password' => Hash::make('8235918KSJH'),
            'role_id' => $userRole,
        ]);

        User::create([
            'first_name' => 'Степан',
            'last_name' => 'Томилов',
            'email' => 'tomilka322@gmail.com',
            'password' => Hash::make('stepantop228'),
            'role_id' => $userRole,
        ]);
    }
}

