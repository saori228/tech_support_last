<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSupport() || $user->isAdmin()) {
            $tickets = Ticket::with(['user', 'status'])->get();
            return view('tickets.index', compact('tickets'));
        }
        
        $tickets = Ticket::where('user_id', $user->id)->with('status')->get();
        return view('tickets.index', compact('tickets'));
    }
    
    public function create()
    {
        return view('tickets.create');
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'error_datetime' => 'required|date',
            'description' => 'required|string',
            'error_text' => 'required|string',
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
