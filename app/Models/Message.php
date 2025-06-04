<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'support_id',
        'content',
        'attachment',
        'is_from_user',
        'is_read',
    ];
    
    protected $casts = [
        'is_from_user' => 'boolean',
        'is_read' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function support()
    {
        return $this->belongsTo(User::class, 'support_id');
    }
    
    // Метод для получения сообщений между пользователем и сотрудником
    public static function getBetweenUserAndSupport($userId, $supportId)
    {
        return self::where(function($query) use ($userId, $supportId) {
            $query->where(function($q) use ($userId, $supportId) {
                $q->where('user_id', $userId)
                  ->where('support_id', $supportId);
            })->orWhere(function($q) use ($userId, $supportId) {
                $q->where('user_id', $supportId)
                  ->where('support_id', $userId);
            });
        })->orderBy('created_at')->get();
    }
    
    // Метод для получения всех сообщений пользователя
    public static function getAllForUser($userId)
    {
        return self::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhere('support_id', $userId);
        })->orderBy('created_at')->get();
    }
    
    // Метод для отметки сообщений как прочитанных
    public static function markAsReadBetweenUsers($fromUserId, $toUserId)
    {
        return self::where('user_id', $fromUserId)
            ->where('support_id', $toUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
    
    // Метод для проверки наличия непрочитанных сообщений от пользователя к сотруднику
    public static function hasUnreadMessages($fromUserId, $toUserId, $isFromUser = true)
    {
        return self::where('user_id', $fromUserId)
            ->where('support_id', $toUserId)
            ->where('is_from_user', $isFromUser)
            ->where('is_read', false)
            ->exists();
    }
    
    // Метод для получения количества непрочитанных сообщений
    public static function getUnreadCount($fromUserId, $toUserId, $isFromUser = true)
    {
        return self::where('user_id', $fromUserId)
            ->where('support_id', $toUserId)
            ->where('is_from_user', $isFromUser)
            ->where('is_read', false)
            ->count();
    }
    
    // Метод для получения пользователей с непрочитанными сообщениями для сотрудника
    public static function getUsersWithUnreadMessages($supportId)
    {
        return self::where('support_id', $supportId)
            ->where('is_from_user', true)
            ->where('is_read', false)
            ->where('created_at', '>', now()->subHours(24))
            ->distinct()
            ->pluck('user_id');
    }
}