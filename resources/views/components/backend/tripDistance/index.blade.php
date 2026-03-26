<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            Fahrtenbuch – Kurs auswählen
        </h2>
    </x-slot>

    {{-- Kursliste zur Auswahl --}}
    <div class="main-box">
        @if($coursedates->isEmpty())
            <p class="form-label">Keine Kurstermine für die aktuelle Auswahl gefunden.</p>
        @else
            <div class="dashboard-flexbox">
                @foreach($coursedates as $coursedate)
                    <div class="dashboard-flexbox-b1-2">
                        <div class="dashboard-flexbox-text">

                            <div class="dasboard-iconbox">
                                <a class="dasboard-iconbox-a"
                                   href="{{ route('backend.tripDistance.show', ['coursedate' => $coursedate->id, 'all_courses' => $showAll ? 1 : 0]) }}"
                                   title="Fahrtenbuch öffnen">
                                    <box-icon name='line-chart'></box-icon>
                                </a>
                            </div>

                            <label class="label">Kurs:</label>
                            {{ $coursedate->course->kursName ?? '–' }}<br>

                            <label class="label">Termin:</label>
                            {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }}
                            – {{ date('H:i', strtotime($coursedate->kursendtermin)) }} Uhr<br>

                            <label class="label">Distanz:</label>
                            {{ number_format((float) $coursedate->kursFahrtenlaenge, 2, ',', '') }} km<br>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-app-layout>


