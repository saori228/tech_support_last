@extends('layouts.app')

@section('content')
    <div class="form-container">
        <div class="form-content">
            <form action="{{ route('tickets.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="datetime-local" name="error_datetime" class="form-input" placeholder="Дата и время ошибки" value="{{ old('error_datetime') }}" required>
                </div>
                <div class="form-group">
                    <textarea name="description" class="form-input" placeholder="Описание проблемы" required>{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <textarea name="error_text" class="form-input" placeholder="Текст ошибки" required>{{ old('error_text') }}</textarea>
                </div>
                <button type="submit" class="button">Создать обращение</button>
            </form>
        </div>
    </div>
@endsection
