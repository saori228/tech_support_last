<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isSupport()) {
            // Получаем всех пользователей (не сотрудников и не админов)
            $userRole = Role::where('name', 'пользователь')->first();
            $users = User::where('role_id', $userRole->id)->get();
            
            // Если нет пользователей, возвращаем пустой чат
            if ($users->isEmpty()) {
                return view('chat.index', ['users' => collect(), 'messages' => collect()]);
            }
            
            // Получаем текущего выбранного пользователя или первого в списке
            $currentUserIndex = 0;
            if ($request->has('user_id')) {
                $selectedUser = $users->firstWhere('id', $request->user_id);
                if ($selectedUser) {
                    $currentUserIndex = $users->search(function($item) use ($selectedUser) {
                        return $item->id === $selectedUser->id;
                    });
                } else {
                    $selectedUser = $users->first();
                }
            } else {
                $selectedUser = $users->first();
            }
            
            // Вычисляем индексы предыдущего и следующего пользователя (циклически)
            $prevUserIndex = ($currentUserIndex - 1 + $users->count()) % $users->count();
            $nextUserIndex = ($currentUserIndex + 1) % $users->count();
            
            $prevUser = $users[$prevUserIndex];
            $nextUser = $users[$nextUserIndex];
            
            // Получаем сообщения для выбранного пользователя
            $messages = Message::where(function($query) use ($selectedUser, $user) {
                $query->where(function($q) use ($selectedUser, $user) {
                    $q->where('user_id', $selectedUser->id)
                      ->where('support_id', $user->id);
                })->orWhere(function($q) use ($selectedUser, $user) {
                    $q->where('user_id', $user->id)
                      ->where('support_id', $selectedUser->id);
                });
            })->orderBy('created_at')->get();
            
            return view('chat.index', compact('users', 'selectedUser', 'messages', 'prevUser', 'nextUser'));
        } elseif ($user->isAdmin()) {
            // Для админа показываем его чат с сотрудником поддержки
            $supportRole = Role::where('name', 'сотрудник')->first();
            $supportUsers = User::where('role_id', $supportRole->id)->get();
            
            // Если нет сотрудников, возвращаем пустой чат
            if ($supportUsers->isEmpty()) {
                return view('chat.index', ['messages' => collect()]);
            }
            
            // Получаем текущего выбранного сотрудника или первого в списке
            $currentSupportIndex = 0;
            if ($request->has('support_id')) {
                $selectedSupport = $supportUsers->firstWhere('id', $request->support_id);
                if ($selectedSupport) {
                    $currentSupportIndex = $supportUsers->search(function($item) use ($selectedSupport) {
                        return $item->id === $selectedSupport->id;
                    });
                } else {
                    $selectedSupport = $supportUsers->first();
                }
            } else {
                $selectedSupport = $supportUsers->first();
            }
            
            // Вычисляем индексы предыдущего и следующего сотрудника (циклически)
            $prevSupportIndex = ($currentSupportIndex - 1 + $supportUsers->count()) % $supportUsers->count();
            $nextSupportIndex = ($currentSupportIndex + 1) % $supportUsers->count();
            
            $prevSupport = $supportUsers[$prevSupportIndex];
            $nextSupport = $supportUsers[$nextSupportIndex];
            
            $messages = Message::where(function($query) use ($user, $selectedSupport) {
                $query->where(function($q) use ($user, $selectedSupport) {
                    $q->where('user_id', $user->id)
                      ->where('support_id', $selectedSupport->id);
                })->orWhere(function($q) use ($user, $selectedSupport) {
                    $q->where('user_id', $selectedSupport->id)
                      ->where('support_id', $user->id);
                });
            })->orderBy('created_at')->get();
            
            return view('chat.index', compact('messages', 'supportUsers', 'selectedSupport', 'prevSupport', 'nextSupport'));
        } else {
            // Для обычного пользователя показываем его чат с сотрудником поддержки
            $supportRole = Role::where('name', 'сотрудник')->first();
            $supportUser = User::where('role_id', $supportRole->id)->first();
            
            $messages = Message::where(function($query) use ($user, $supportUser) {
                if ($supportUser) {
                    $query->where(function($q) use ($user, $supportUser) {
                        $q->where('user_id', $user->id)
                          ->where('support_id', $supportUser->id);
                    })->orWhere(function($q) use ($user, $supportUser) {
                        $q->where('user_id', $supportUser->id)
                          ->where('support_id', $user->id);
                    });
                } else {
                    $query->where('user_id', $user->id);
                }
            })->orderBy('created_at')->get();
            
            return view('chat.index', compact('messages', 'supportUser'));
        }
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:2048',
            'recipient_id' => 'nullable|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $attachmentPath = null;
        
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }
        
        if ($user->isSupport()) {
            // Сотрудник отправляет сообщение пользователю
            Message::create([
                'user_id' => $request->recipient_id,
                'support_id' => $user->id,
                'content' => $request->content,
                'attachment' => $attachmentPath,
                'is_from_user' => false,
            ]);
            
            return redirect()->route('chat.index', ['user_id' => $request->recipient_id]);
        } elseif ($user->isAdmin()) {
            // Админ отправляет сообщение сотруднику поддержки
            Message::create([
                'user_id' => $user->id,
                'support_id' => $request->recipient_id,
                'content' => $request->content,
                'attachment' => $attachmentPath,
                'is_from_user' => true,
            ]);
            
            return redirect()->route('chat.index', ['support_id' => $request->recipient_id]);
        } else {
            // Пользователь отправляет сообщение сотруднику поддержки
            $supportRole = Role::where('name', 'сотрудник')->first();
            $supportUser = User::where('role_id', $supportRole->id)->first();
            
            Message::create([
                'user_id' => $user->id,
                'support_id' => $supportUser ? $supportUser->id : null,
                'content' => $request->content,
                'attachment' => $attachmentPath,
                'is_from_user' => true,
            ]);
            
            return redirect()->route('chat.index');
        }
    }
}