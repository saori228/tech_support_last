@extends('layouts.app')

@section('content')
    <div class="ticket-list">
        @forelse($tickets as $ticket)
            <div class="ticket-item">
                <div class="ticket-left">
                    <div class="ticket-number">Номер обращения: {{ $ticket->ticket_number }}</div>
                    <div class="ticket-deadline">
                        @if($ticket->status->name === 'Завершено')
                            Время обработки обращения: завершено
                        @elseif($ticket->status->name === 'Приостановлено')
                            Время обработки обращения: приостановлено
                        @else
                            Время обработки обращения до: {{ $ticket->processing_deadline->format('d.m.Y') }} включительно
                        @endif
                    </div>
                    
                    @if(auth()->user()->isSupport())
                        <form action="{{ route('tickets.update.deadline', $ticket) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="date" name="processing_deadline" value="{{ $ticket->processing_deadline->format('Y-m-d') }}">
                            <button type="submit" class="button">Обновить срок</button>
                        </form>
                        
                        <form action="{{ route('tickets.update.status', $ticket) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status_id">
                                @foreach(\App\Models\TicketStatus::all() as $status)
                                    <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="button">Обновить статус</button>
                        </form>
                    @endif
                </div>
                <div class="ticket-right">
                    <div class="ticket-description">{{ $ticket->description }}</div>
                </div>
            </div>
        @empty
            <div style="padding: 20px; text-align: center;">У вас пока нет обращений</div>
        @endforelse
    </div>
@endsection
