@component('mail::message')

# Invitation à rejoindre une colocation

Bonjour,

Vous avez été invité(e) à rejoindre la colocation **{{ $invitation->colocation->name }}** par son propriétaire.

Cliquez sur le bouton ci-dessous pour voir et accepter (ou refuser) l'invitation :

@component('mail::button', ['url' => route('invitations.show', $invitation->token)])
Voir l'invitation
@endcomponent

Cette invitation expire le **{{ $invitation->expires_at->format('d/m/Y à H:i') }}**.

Si le bouton ne fonctionne pas, copiez-collez ce lien :  
{{ route('invitations.show', $invitation->token) }}

Cordialement,  
L'équipe EasyColoc

@endcomponent