<x-mail::message>
# Wichtige Terminänderung
Hallo {{ $participant->vorname }},

der Termin **{{ $coursedate->getCousename->kursName }}** wurde wegen Schlechtwetter angepasst.

@if($actionType === 'cancel')
**Status:** Abgesagt

**Ursprünglicher Termin:**  
{{ $oldStart->format('d.m.Y H:i') }} Uhr bis {{ $oldEnd->format('d.m.Y H:i') }} Uhr
@else
**Status:** Verschoben

**Bisheriger Termin:**  
{{ $oldStart->format('d.m.Y H:i') }} Uhr bis {{ $oldEnd->format('d.m.Y H:i') }} Uhr

**Neuer Termin:**  
{{ \Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('d.m.Y H:i') }} Uhr bis {{ \Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('d.m.Y H:i') }} Uhr
@endif

**Nachricht vom Trainer:**  
{{ $trainerMessage }}

Vielen Dank für dein Verständnis.

{{ config('app.name') }}<br>
{{ env('VEREIN_NAME') }}<br>
@include('textimport.mailImpressum')

</x-mail::message>
