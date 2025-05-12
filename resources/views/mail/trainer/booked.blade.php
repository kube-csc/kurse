<x-mail::message>
# Information zu den Terminen
Hallo {{ $trainername->getKursTrainer->vorname }},

du hast folgende Termin(e) eingestellt:

{!! $mailtext !!}

<!--
<x-mail::button :url="''">
Button Text
</x-mail::button>
-->
Wir bedanken uns für die Organisation der Termine.

{{ config('app.name') }}<br>
{{ env('VEREIN_NAME') }}<br>
@include('textimport.mailImpressum')

</x-mail::message>
