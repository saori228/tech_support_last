<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isSupport()) {
            // Получаем всех пользователей (не сотрудников и не админов)
            $userRole = Role::where('name', 'пользователь')->first();
            $users = User::where('role_id', $userRole->id)->get();
            
            // Если нет пользователей, возвращаем пустой список обращений
            if ($users->isEmpty()) {
                return view('tickets.index', ['users' => collect(), 'tickets' => collect()]);
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
            
            // Получаем обращения для выбранного пользователя
            $tickets = Ticket::where('user_id', $selectedUser->id)->with('status')->get();
            
            return view('tickets.index', compact('users', 'selectedUser', 'tickets', 'prevUser', 'nextUser'));
        } elseif ($user->isAdmin()) {
            // Для админа показываем все обращения
            $tickets = Ticket::with(['user', 'status'])->get();
            return view('tickets.index', compact('tickets'));
        } else {
            // Для обычного пользователя показываем только его обращения
            $tickets = Ticket::where('user_id', $user->id)->with('status')->get();
            return view('tickets.index', compact('tickets'));
        }
    }
    
    public function create()
    {
        // Проверяем, что пользователь не админ
        if (Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Администратор не может создавать обращения');
        }
        
        return view('tickets.create');
    }
    
    public function store(Request $request)
    {
        // Проверяем, что пользователь не админ
        if (Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Администратор не может создавать обращения');
        }
        
        $validator = Validator::make($request->all(), [
            'error_datetime' => 'required|date|before_or_equal:now',
            'description' => 'required|string',
            'error_text' => 'required|string',
        ], [
            'error_datetime.before_or_equal' => 'Дата и время возникновения ошибки не могут быть в будущем',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $status = TicketStatus::where('name', 'В обработке')->first();
        
        // Генерация уникального номера обращения от 1 до 999
        do {
            $ticketNumber = rand(1, 999);
        } while (Ticket::where('ticket_number', $ticketNumber)->exists());
        
        Ticket::create([
            'ticket_number' => $ticketNumber,
            'user_id' => Auth::id(),
            'description' => $request->description,
            'error_text' => $request->error_text,
            'error_datetime' => $request->error_datetime,
            'processing_deadline' => now()->addDays(3),
            'status_id' => $status->id,
        ]);
        
        return redirect()->route('tickets.index')->with('success', 'Обращение успешно создано');
    }
    
    public function updateDeadline(Request $request, Ticket $ticket)
    {
        // Проверяем, что пользователь - сотрудник поддержки
        if (!Auth::user()->isSupport()) {
            return redirect()->back()->with('error', 'У вас нет прав для выполнения этого действия');
        }
        
        $validator = Validator::make($request->all(), [
            'processing_deadline' => 'required|date',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $ticket->processing_deadline = $request->processing_deadline;
        $ticket->save();
        
        return redirect()->back()->with('success', 'Срок обработки обращения обновлен');
    }
    
    public function updateStatus(Request $request, Ticket $ticket)
    {
        // Проверяем, что пользователь - сотрудник поддержки
        if (!Auth::user()->isSupport()) {
            return redirect()->back()->with('error', 'У вас нет прав для выполнения этого действия');
        }
        
        $validator = Validator::make($request->all(), [
            'status_id' => 'required|exists:ticket_statuses,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $ticket->status_id = $request->status_id;
        $ticket->save();
        
        return redirect()->back()->with('success', 'Статус обращения обновлен');
    }
}