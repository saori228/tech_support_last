@extends('layouts.app')

@section('content')
    <div class="create-ticket">
        <h2 class="title">Создать обращение</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('tickets.store') }}" method="POST" class="ticket-form">
            @csrf
            
            <div class="form-group">
                <label for="error_datetime">Дата и время возникновения ошибки:</label>
                <input type="datetime-local" id="error_datetime" name="error_datetime" required 
                       max="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('error_datetime', now()->format('Y-m-d\TH:i')) }}"
                       class="ticket-input">
            </div>
            
            <div class="form-group">
                <label for="description">Описание проблемы:</label>
                <textarea id="description" name="description" required class="ticket-textarea">{{ old('description') }}</textarea>
            </div>
            
            <div class="form-group">
                <label for="error_text">Текст ошибки:</label>
                <textarea id="error_text" name="error_text" required class="ticket-textarea">{{ old('error_text') }}</textarea>
            </div>
            
            <button type="submit" class="button">Создать обращение</button>
        </form>
    </div>
    
    <style>
        .create-ticket {
            width: 100%;
            max-width: 600px;
        }
        
        .ticket-form {
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .ticket-input, .ticket-textarea {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background-color: #000;
            color: #fff;
        }
        
        .ticket-textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .alert-danger {
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 480px) {
            .create-ticket .title {
                font-size: 20px;
                margin-bottom: 15px;
            }
            
            .form-group {
                margin-bottom: 15px;
            }
            
            .ticket-input, .ticket-textarea {
                padding: 8px;
                border-radius: 8px;
            }
            
            .ticket-textarea {
                min-height: 80px;
            }
        }
    </style>
    
    <script>
        // Устанавливаем максимальную дату для выбора - текущая дата и время
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const maxDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            document.getElementById('error_datetime').max = maxDateTime;
            
            // Дополнительная проверка при отправке формы
            document.querySelector('form').addEventListener('submit', function(e) {
                const selectedDate = new Date(document.getElementById('error_datetime').value);
                const currentDate = new Date();
                
                if (selectedDate > currentDate) {
                    e.preventDefault();
                    alert('Дата и время возникновения ошибки не могут быть в будущем!');
                }
            });
        });
    </script>
@endsection