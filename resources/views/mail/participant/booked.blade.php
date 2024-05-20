<x-mail::message>
# Information zu den Terminen

Hallo {{ $courseParticipants->vorname }},<br>

Du hast folgende Termine gebucht: <br> <br>

{!! $mailtext !!}

<!--
<x-mail::button :url="''">
Button Text
</x-mail::button>
-->

Wir bedanken uns für die Buchung eines Termins,<br>
{{ config('app.name') }}
</x-mail::message>
