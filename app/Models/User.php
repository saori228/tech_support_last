<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // Закомментировано, так как пакет не установлен

class User extends Authenticatable
{
    // use HasApiTokens, HasFactory, Notifiable; // Закомментировано, так как пакет не установлен
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function isAdmin()
    {
        // Исправленный метод для проверки роли админа
        // Проверяем как по имени "администратор", так и по имени "admin"
        return $this->role && ($this->role->name === 'администратор' || $this->role->name === 'admin');
    }

    public function isSupport()
    {
        return $this->role && $this->role->name === 'сотрудник';
    }

    public function isUser()
    {
        return $this->role && $this->role->name === 'пользователь';
    }
    
    // Метод для получения всех сообщений пользователя
    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id')
            ->orWhere('support_id', $this->id);
    }
}