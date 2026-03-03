<div class="mt-6 border-t pt-6">
    <h3 class="text-lg font-medium">Inviter quelqu'un</h3>
    <p class="text-sm text-gray-500 mb-4">Entrez l'adresse email de la personne à inviter</p>

    <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="flex flex-col sm:flex-row gap-3">
        @csrf

        <input type="email" name="email" required
               placeholder="email@exemple.com"
               class="flex-1 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
            Envoyer l'invitation
        </button>
    </form>

    @error('email')
        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>