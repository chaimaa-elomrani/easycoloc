<div class="container mx-auto py-8">
    <h1>{{ $colocation->name }}</h1>
    <p>Statut : {{ $colocation->status }}</p>
    <p>Description : {{ $colocation->description ?? 'Aucune' }}</p>

    <!-- Actions owner only -->
    @if (auth()->id() === $colocation->owner_id)
        <div class="mb-6 space-x-4">
            <a href="{{ route('colocations.edit', $colocation) }}" class="btn btn-warning">Modifier</a>

            <form action="{{ route('colocations.cancel', $colocation) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('Annuler la colocation ?')">Annuler</button>
            </form>

            <form action="{{ route('colocations.destroy', $colocation) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-dark" onclick="return confirm('Supprimer définitivement ?')">Supprimer</button>
            </form>
        </div>
    @endif

    <!-- Liste des membres (visible par tous les membres actifs) -->
    <h2>Membres actifs ({{ $colocation->activeMembers()->count() }})</h2>

    @if ($colocation->activeMembers()->count() > 0)
        <ul class="list-group">
            @foreach ($colocation->activeMembers as $member)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $member->name }}
                    - Rôle : {{ $member->pivot->role }}
                    - Réputation : {{ $member->reputation }}

                    @if (auth()->id() === $colocation->owner_id && $member->id !== auth()->id())
                        <form action="{{ route('memberships.remove', [$colocation, $member->id]) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Retirer ce membre ?')">Retirer</button>
                        </form>
                    @endif

                    @if ($member->id === auth()->id())
                        <form action="{{ route('memberships.leave', $colocation) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Quitter la colocation ?')">Quitter</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p>Aucun membre actif pour le moment.</p>
    @endif

    @include('partials.invitateForm')
</div>