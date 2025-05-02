@extends('layouts.app')

@section('content')
    <a href="{{ route('tickets.index') }}" class="button">Обращения</a>
    <a href="{{ route('tickets.create') }}" class="button">Создать обращение</a>
    <a href="{{ route('chat.index') }}" class="button">Чат</a>
@endsection
