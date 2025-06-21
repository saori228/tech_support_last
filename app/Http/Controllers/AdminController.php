<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Добавляем логирование для отладки
        Log::info('AdminController: Checking if user is admin', [
            'user_id' => $user->id,
            'user_role' => $user->role->name ?? 'no role',
            'is_admin_method' => $user->isAdmin(),
        ]);
        // ?? позволяет выбрать одно из двух выражений в зависимости от условия
        // Проверяем роль напрямую, чтобы обойти возможные проблемы с методом isAdmin()
        if ($user->role && ($user->role->name === 'администратор' || $user->role->name === 'admin')) {
            // Поиск пользователей по email и ФИО
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $users = User::where(function($query) use ($searchTerm) {
                    $query->where('email', 'like', '%' . $searchTerm . '%') // where добавляет условие выборки к запросу в бд
                          ->orWhere('first_name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                          ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchTerm . '%'); // CONCAT используется для объединения двух или более строковых значений в одну строку.
                }) //orWhere - позволяет добавить условие в бд //raw - позволяет записать в бд сырое выражение (необработанное) в виде строки, то есть готовые выражения
                ->with('role')
                ->where('id', '!=', Auth::id())
                ->get(); // обработка запроса
                
                if ($request->ajax()) {
                    return response()->json($users); // response - возврат строки из контроллера
                }
            } else {
                $users = User::with('role')->where('id', '!=', Auth::id())->get();
            }
            
            $roles = Role::all();
            
            return view('admin.index', compact('users', 'roles')); 
        }
        
        return redirect()->route('home')->with('error', 'У вас нет прав для доступа к этой странице');
    }
    
    public function updateRole(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        // Проверяем роль напрямую
        if (!$currentUser->role || !($currentUser->role->name === 'администратор' || $currentUser->role->name === 'admin')) {
            return redirect()->route('home')->with('error', 'У вас нет прав для выполнения этого действия');
        }
        
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        
        $oldRoleId = $user->role_id;
        $newRoleId = $request->role_id;
        
        // Начинаем транзакцию для обеспечения целостности данных // данный метод используется для того, чтобы запрос в бд шёл как единая целая последовательность
        DB::beginTransaction();
        
        try {
            // Обновляем роль пользователя
            $user->role_id = $newRoleId;
            $user->save();
            
            // Получаем роли
            $userRole = Role::where('name', 'пользователь')->first();
            $supportRole = Role::where('name', 'сотрудник')->first();
            
            // Если пользователь стал сотрудником из обычного пользователя
            if ($oldRoleId == $userRole->id && $newRoleId == $supportRole->id) {
                // Ничего не делаем с сообщениями, они должны остаться видимыми
                // для нового сотрудника в его чатах с другими пользователями
            }
            
            // Если сотрудник стал обычным пользователем
            if ($oldRoleId == $supportRole->id && $newRoleId == $userRole->id) {
                // Ничего не делаем с сообщениями, они должны остаться в истории
                // но новые сообщения будут идти к новому сотруднику
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Роль пользователя обновлена');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user role', [
                'user_id' => $user->id,
                'old_role_id' => $oldRoleId,
                'new_role_id' => $newRoleId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при обновлении роли: ' . $e->getMessage());
        }
    }
} // catch - позволяет обрабатывать ошибки возникающие во время выполнения программы //rollback - отменяет все текущие изменения транзакции внесённые в бд
