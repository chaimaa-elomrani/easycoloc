<!-- edit.blade.php -->
@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Modifier {{ $colocation->name }}</h1>
        <!-- Même formulaire que create, mais avec value="{{ old('name', $colocation->name) }}" -->
    </div>
@endsection