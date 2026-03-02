@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Créer une colocation</h1>

        <form method="POST" action="{{ route('colocations.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nom de la colocation</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description (optionnel)</label>
                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Créer</button>
        </form>
    </div>
@endsection