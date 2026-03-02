@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-8">
        <h1>Modifier {{ $colocation->name }}</h1>

        <form method="POST" action="{{ route('colocations.update', $colocation) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nom de la colocation</label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $colocation->name) }}" 
                       class="w-full border rounded px-3 py-2" required>
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" 
                          class="w-full border rounded px-3 py-2">{{ old('description', $colocation->description ?? '') }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Enregistrer les modifications
            </button>

            <a href="{{ route('colocations.show', $colocation) }}" class="ml-4 text-gray-600 hover:underline">
                Annuler
            </a>
        </form>
    </div>
@endsection