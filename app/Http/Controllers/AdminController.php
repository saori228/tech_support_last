<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home');
        }
        
        $users = User::with('role')->where('id', '!=', Auth::id())->get();
        $roles = Role::all();
        
        return view('admin.index', compact('users', 'roles'));
    }
    
    public function updateRole(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home');
        }
        
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        
        $user->role_id = $request->role_id;
        $user->save();
        
        return redirect()->back()->with('success', 'Роль пользователя обновлена');
    }
}
