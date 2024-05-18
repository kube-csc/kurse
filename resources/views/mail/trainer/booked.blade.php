<x-mail::message>
# Information zu den Terminen

Hallo {{ $trainer->getKursTrainer->vorname }},<br>

Du hast folgende Termine eingestellt: <br> <br>

{!! $mailtext !!}

<!--
<x-mail::button :url="''">
Button Text
</x-mail::button>
-->

Wir bedanken uns für die Organisation der Termine.,<br>
{{ config('app.name') }}<br>
{{ env('VEREIN_NAME') }}


</x-mail::message>
