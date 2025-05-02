<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSupport() || $user->isAdmin()) {
            $users = User::where('role_id', '!=', $user->role_id)->get();
            $selectedUser = request('user_id') ? User::find(request('user_id')) : $users->first();
            
            if ($selectedUser) {
                $messages = Message::where(function($query) use ($selectedUser) {
                    $query->where('user_id', $selectedUser->id)
                          ->orWhere('support_id', $selectedUser->id);
                })->orderBy('created_at')->get();
                
                return view('chat.index', compact('users', 'selectedUser', 'messages'));
            }
            
            return view('chat.index', compact('users'));
        }
        
        $messages = Message::where('user_id', $user->id)
                          ->orWhere('support_id', $user->id)
                          ->orderBy('created_at')
                          ->get();
        
        return view('chat.index', compact('messages'));
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
        
        if ($user->isSupport() || $user->isAdmin()) {
            Message::create([
                'user_id' => $request->recipient_id,
                'support_id' => $user->id,
                'content' => $request->content,
                'attachment' => $attachmentPath,
                'is_from_user' => false,
            ]);
        } else {
            // Находим сотрудника поддержки для этого пользователя
            $support = User::whereHas('role', function($query) {
                $query->where('name', 'сотрудник');
            })->first();
            
            Message::create([
                'user_id' => $user->id,
                'support_id' => $support ? $support->id : null,
                'content' => $request->content,
                'attachment' => $attachmentPath,
                'is_from_user' => true,
            ]);
        }
        
        return redirect()->back();
    }
}
