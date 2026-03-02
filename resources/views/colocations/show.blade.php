@if ($colocation->owner_id === auth()->id())
    <a href="{{ route('colocations.edit', $colocation) }}" class="btn btn-warning">Modifier</a>

    <form action="{{ route('colocations.cancel', $colocation) }}" method="POST" style="display:inline;">
        @csrf
        @method('POST')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Annuler la colocation ?')">Annuler</button>
    </form>

    <form action="{{ route('colocations.destroy', $colocation) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-dark" onclick="return confirm('Supprimer définitivement ?')">Supprimer</button>
    </form>
@endif