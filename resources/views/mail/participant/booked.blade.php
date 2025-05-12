<x-mail::message>
# Information zu den Buchungen
Hallo {{ $courseParticipants->vorname }},

du hast folgende Termin(e) gebucht:

{!! $mailtext !!}

<!--
<x-mail::button :url="''">
Button Text
</x-mail::button>
-->
Wir bedanken uns für die Buchungen.

{{ config('app.name') }}<br>
{{ env('VEREIN_NAME') }}<br>
@include('textimport.mailImpressum')

</x-mail::message>
