@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Mes colocations</h1>

        @if ($colocations->isEmpty())
            <p>Aucune colocation pour le moment.</p>
            <a href="{{ route('colocations.create') }}" class="btn btn-primary">Créer une colocation</a>
        @else
            <ul>
                @foreach ($colocations as $coloc)
                    <li>
                        <a href="{{ route('colocations.show', $coloc) }}">
                            {{ $coloc->name }} ({{ $coloc->status }})
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection