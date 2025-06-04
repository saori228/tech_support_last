<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isSupport()) {
            // Поиск пользователей для сотрудника поддержки
            if ($request->has('search')) {
                $userRole = Role::where('name', 'пользователь')->first();
                $searchTerm = $request->search;
                
                if (empty($searchTerm)) {
                    // Показываем всех пользователей с приоритетом для тех, кто писал недавно
                    $searchResults = User::where('role_id', $userRole->id)
                        ->leftJoin('messages', function($join) use ($user) {
                            $join->on('users.id', '=', 'messages.user_id')
                                 ->where('messages.is_from_user', true)
                                 ->where('messages.support_id', $user->id)
                                 ->where('messages.created_at', '>', now()->subHours(24));
                        })
                        ->select('users.*', DB::raw('COUNT(messages.id) as recent_messages_count'))
                        ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.role_id', 'users.created_at', 'users.updated_at')
                        ->orderByDesc('recent_messages_count')
                        ->orderBy('users.first_name')
                        ->limit(10)
                        ->get();
                } else {
                    // Поиск по email и ФИО
                    $searchResults = User::where('role_id', $userRole->id)
                        ->where(function($query) use ($searchTerm) {
                            $query->where('email', 'like', '%' . $searchTerm . '%')
                                  ->orWhere('first_name', 'like', '%' . $searchTerm . '%')
                                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                                  ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchTerm . '%');
                        })
                        ->get();
                }
                
                // Добавляем информацию о новых сообщениях
                foreach ($searchResults as $searchUser) {
                    // Проверяем, открывал ли сотрудник чат с этим пользователем недавно
                    $lastViewTime = session('last_view_' . $searchUser->id, now()->subDays(1));
                    
                    $searchUser->has_new_messages = Message::where('user_id', $searchUser->id)
                        ->where('support_id', $user->id)
                        ->where('is_from_user', true)
                        ->where('created_at', '>', $lastViewTime)
                        ->exists();
                    
                    $searchUser->unread_count = Message::where('user_id', $searchUser->id)
                        ->where('support_id', $user->id)
                        ->where('is_from_user', true)
                        ->where('created_at', '>', $lastViewTime)
                        ->count();
                }
                
                return response()->json($searchResults);
            }
            
            // Получаем всех пользователей
            $userRole = Role::where('name', 'пользователь')->first();
            $users = User::where('role_id', $userRole->id)->get();
            
            if ($users->isEmpty()) {
                return view('chat.index', ['users' => collect(), 'messages' => collect()]);
            }
            
            // Получаем выбранного пользователя
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
            
            $prevUserIndex = ($currentUserIndex - 1 + $users->count()) % $users->count();
            $nextUserIndex = ($currentUserIndex + 1) % $users->count();
            
            $prevUser = $users[$prevUserIndex];
            $nextUser = $users[$nextUserIndex];
            
            // Получаем сообщения
            $messages = Message::getBetweenUserAndSupport($selectedUser->id, $user->id);
            
            // Сохраняем время просмотра чата с этим пользователем
            session(['last_view_' . $selectedUser->id => now()]);
            
            return view('chat.index', compact('users', 'selectedUser', 'messages', 'prevUser', 'nextUser'));
            
        } elseif ($user->isAdmin()) {
            // Поиск сотрудников для админа
            if ($request->has('search')) {
                $supportRole = Role::where('name', 'сотрудник')->first();
                $searchTerm = $request->search;
                
                if (empty($searchTerm)) {
                    $searchResults = User::where('role_id', $supportRole->id)
                        ->orderBy('first_name')
                        ->limit(10)
                        ->get();
                } else {
                    $searchResults = User::where('role_id', $supportRole->id)
                        ->where(function($query) use ($searchTerm) {
                            $query->where('email', 'like', '%' . $searchTerm . '%')
                                  ->orWhere('first_name', 'like', '%' . $searchTerm . '%')
                                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                                  ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchTerm . '%');
                        })
                        ->get();
                }
                
                return response()->json($searchResults);
            }
            
            // Для админа показываем чат с сотрудниками
            $supportRole = Role::where('name', 'сотрудник')->first();
            $supportUsers = User::where('role_id', $supportRole->id)->get();
            
            if ($supportUsers->isEmpty()) {
                return view('chat.index', ['messages' => collect()]);
            }
            
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
            
            $prevSupportIndex = ($currentSupportIndex - 1 + $supportUsers->count()) % $supportUsers->count();
            $nextSupportIndex = ($currentSupportIndex + 1) % $supportUsers->count();
            
            $prevSupport = $supportUsers[$prevSupportIndex];
            $nextSupport = $supportUsers[$nextSupportIndex];
            
            $messages = Message::getBetweenUserAndSupport($user->id, $selectedSupport->id);
            
            return view('chat.index', compact('messages', 'supportUsers', 'selectedSupport', 'prevSupport', 'nextSupport'));
            
        } else {
            // Для обычного пользователя
            $supportRole = Role::where('name', 'сотрудник')->first();
            $supportUser = User::where('role_id', $supportRole->id)->first();
            
            if ($supportUser) {
                $messages = Message::getBetweenUserAndSupport($user->id, $supportUser->id);
            } else {
                $messages = collect();
            }
            
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
            Message::create([
                'user_id' => $request->recipient_id,
                'support_id' => $user->id,
                'content' => $request->content,
                'attachment' => $attachmentPath,
                'is_from_user' => false,
            ]);
            
            return redirect()->route('chat.index', ['user_id' => $request->recipient_id]);
        } elseif ($user->isAdmin()) {
            Message::create([
                'user_id' => $user->id,
                'support_id' => $request->recipient_id,
                'content' => $request->content,
                'attachment' => $attachmentPath,
                'is_from_user' => true,
            ]);
            
            return redirect()->route('chat.index', ['support_id' => $request->recipient_id]);
        } else {
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