<!-- show.blade.php -->
@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>{{ $colocation->name }}</h1>
        <p>Statut : {{ $colocation->status }}</p>
        <p>Description : {{ $colocation->description ?? 'Aucune' }}</p>
    </div>
@endsection