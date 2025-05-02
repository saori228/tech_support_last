@extends('layouts.app')

@section('content')
    <div class="chat-container">
        <div class="chat-title">ЧАТ</div>
        
        @if(auth()->user()->isSupport())
            <div style="padding: 10px; display: flex; justify-content: space-between;">
                <button onclick="window.location.href='{{ route('chat.index', ['user_id' => isset($users) && $users->count() > 0 ? $users->first()->id : '']) }}'" style="background: none; border: none; cursor: pointer;">
                    &lt; Предыдущий
                </button>
                <div>{{ isset($selectedUser) ? $selectedUser->first_name . ' ' . $selectedUser->last_name : 'Нет пользователей' }}</div>
                <button onclick="window.location.href='{{ route('chat.index', ['user_id' => isset($users) && $users->count() > 1 ? $users->get(1)->id : '']) }}'" style="background: none; border: none; cursor: pointer;">
                    Следующий &gt;
                </button>
            </div>
        @endif
        
        <div class="chat-messages">
            @if(isset($messages))
                @foreach($messages as $message)
                    <div class="message {{ $message->is_from_user ? 'message-user' : 'message-support' }}">
                        @if($message->is_from_user)
                            <div>{{ auth()->id() == $message->user_id ? 'Я' : $message->user->first_name }}</div>
                        @else
                            <div>{{ auth()->id() == $message->support_id ? '' : 'Сотрудник' }}</div>
                        @endif
                        <div>{{ $message->content }}</div>
                        @if($message->attachment)
                            <div>
                                <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank">Вложение</a>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
        
        <form action="{{ route('chat.store') }}" method="POST" enctype="multipart/form-data" class="chat-input">
            @csrf
            @if(auth()->user()->isSupport() && isset($selectedUser))
                <input type="hidden" name="recipient_id" value="{{ $selectedUser->id }}">
            @endif
            <button type="button" onclick="document.getElementById('attachment').click();" style="background: none; border: none;">
                <img src="{{ asset('clip.png') }}" alt="Прикрепить" style="width: 20px; height: 20px;">
            </button>
            <input type="file" id="attachment" name="attachment" style="display: none;">
            <input type="text" name="content" placeholder="Сообщение" required>
            <button type="submit">
                <img src="{{ asset('send.png') }}" alt="Отправить" style="width: 20px; height: 20px;">
            </button>
        </form>
    </div>
@endsection
