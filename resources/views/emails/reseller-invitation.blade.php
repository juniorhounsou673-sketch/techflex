<x-mail::message>
# BIENVENUE DANS L’ÉQUIPE DE TECHFLEX !

Bonjour {{ $user->name }},

TECHFLEX vous donne accès à votre espace revendeur. Cliquez sur **Activer mon compte**, choisissez votre mot de passe, puis entrez directement dans votre espace.

<x-mail::button :url="$url">
Activer mon compte
</x-mail::button>

Votre espace revendeur est aussi accessible depuis [l’espace pro]({{ url('/connexion') }}) avec l’email de cette invitation et le mot de passe que vous aurez choisi.

Ce lien est valable {{ $days }} jours et ne peut être utilisé qu'une seule fois.
Si vous n'êtes pas à l'origine de cette invitation, ignorez cet email.

<x-slot:subcopy>
Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur : [{{ $url }}]({{ $url }})
</x-slot:subcopy>

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
