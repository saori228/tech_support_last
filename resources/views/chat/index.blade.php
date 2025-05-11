@extends('layouts.app')

@section('content')
    <div class="chat-container">
        <div class="chat-title">ЧАТ</div>
        
        @if(auth()->user()->isSupport() && isset($selectedUser))
            <div class="chat-navigation">
                <a href="{{ route('chat.index', ['user_id' => isset($prevUser) ? $prevUser->id : '']) }}" class="nav-link">
                    &lt; Предыдущий
                </a>
                <div class="current-user">{{ isset($selectedUser) ? $selectedUser->first_name . ' ' . $selectedUser->last_name : 'Нет пользователей' }}</div>
                <a href="{{ route('chat.index', ['user_id' => isset($nextUser) ? $nextUser->id : '']) }}" class="nav-link">
                    Следующий &gt;
                </a>
            </div>
        @elseif(auth()->user()->isAdmin() && isset($selectedSupport))
            <div class="chat-navigation">
                <a href="{{ route('chat.index', ['support_id' => isset($prevSupport) ? $prevSupport->id : '']) }}" class="nav-link">
                    &lt; Предыдущий
                </a>
                <div class="current-user">{{ isset($selectedSupport) ? $selectedSupport->first_name . ' ' . $selectedSupport->last_name : 'Нет сотрудников' }}</div>
                <a href="{{ route('chat.index', ['support_id' => isset($nextSupport) ? $nextSupport->id : '']) }}" class="nav-link">
                    Следующий &gt;
                </a>
            </div>
        @endif
        
        <div class="chat-messages">
            @if(isset($messages) && $messages->count() > 0)
                @foreach($messages as $message)
                    <div class="message {{ $message->is_from_user ? 'message-user' : 'message-support' }}">
                        @if($message->is_from_user)
                            <div>{{ auth()->id() == $message->user_id ? 'Я' : ($message->user ? $message->user->first_name : 'Пользователь') }}</div>
                        @else
                            <div>{{ auth()->id() == $message->support_id ? 'Я' : 'Сотрудник' }}</div>
                        @endif
                        <div>{{ $message->content }}</div>
                        @if($message->attachment)
                            <div>
                                <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank">Вложение</a>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="no-messages">Нет сообщений</div>
            @endif
        </div>
        
        <form action="{{ route('chat.store') }}" method="POST" enctype="multipart/form-data" class="chat-input">
            @csrf
            @if(auth()->user()->isSupport() && isset($selectedUser))
                <input type="hidden" name="recipient_id" value="{{ $selectedUser->id }}">
            @elseif(auth()->user()->isAdmin() && isset($selectedSupport))
                <input type="hidden" name="recipient_id" value="{{ $selectedSupport->id }}">
            @endif
            <button type="button" onclick="document.getElementById('attachment').click();" class="attachment-btn">
                <img src="{{ asset('clip.png') }}" alt="Прикрепить" class="attachment-icon">
            </button>
            <input type="file" id="attachment" name="attachment" style="display: none;">
            <input type="text" name="content" placeholder="Сообщение" required class="message-input">
            <button type="submit" class="send-btn">
                <img src="{{ asset('send.png') }}" alt="Отправить" class="send-icon">
            </button>
        </form>
    </div>
    
    <style>
        .chat-navigation {
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-link {
            text-decoration: none;
            color: #000;
        }
        
        .current-user {
            font-weight: bold;
        }
        
        .no-messages {
            text-align: center;
            padding: 20px;
        }
        
        .attachment-btn, .send-btn {
            background: none;
            border: none;
            cursor: pointer;
        }
        
        .attachment-icon, .send-icon {
            width: 20px;
            height: 20px;
        }
        
        .message-input {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
        }
        
        @media (max-width: 480px) {
            .chat-title {
                font-size: 18px;
            }
            
            .chat-messages {
                height: 350px;
            }
            
            .message {
                padding: 8px;
                font-size: 14px;
            }
            
            .message-user {
                border-radius: 15px;
            }
            
            .message-support {
                border-radius: 15px;
            }
            
            .chat-input {
                padding: 8px;
            }
            
            .message-input {
                font-size: 14px;
            }
            
            .attachment-icon, .send-icon {
                width: 18px;
                height: 18px;
            }
        }
    </style>
@endsection