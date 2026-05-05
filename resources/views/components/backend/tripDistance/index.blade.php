<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            Fahrtenbuch – Kurs auswählen
        </h2>
    </x-slot>

    {{-- Kursliste zur Auswahl --}}
    <div class="main-box">
        <div class="dashboard-flexbox">
            <div class="dashboard-flexbox-b1-2">
                <div class="dashboard-flexbox-text">
                    <div class="dasboard-iconbox">
                        <div class="flex items-center gap-2 flex-wrap">
                        @if($prevYear)
                            <a class="dasboard-iconbox-a"
                               href="{{ route('backend.tripDistance.index', ['all_courses' => $showAll ? 1 : 0, 'month' => $prevYear['month'], 'year' => $prevYear['year'], 'nav' => 1]) }}"
                               title="Ein Jahr zurück"
                               aria-label="Ein Jahr zurück">
                                <box-icon name='chevrons-left'></box-icon>
                            </a>
                        @else
                            <span class="dasboard-iconbox-a opacity-40" aria-hidden="true">
                                <box-icon name='chevrons-left'></box-icon>
                            </span>
                        @endif

                        @if($prevMonth)
                            <a class="dasboard-iconbox-a"
                               href="{{ route('backend.tripDistance.index', ['all_courses' => $showAll ? 1 : 0, 'month' => $prevMonth['month'], 'year' => $prevMonth['year'], 'nav' => 1]) }}"
                               title="Ein Monat zurück"
                               aria-label="Ein Monat zurück">
                                <box-icon name='chevron-left'></box-icon>
                            </a>
                        @else
                            <span class="dasboard-iconbox-a opacity-40" aria-hidden="true">
                                <box-icon name='chevron-left'></box-icon>
                            </span>
                        @endif

                        <span class="inline-flex items-center px-2 py-1 text-sm font-semibold text-gray-700 dark:text-gray-200">
                             {{ $currentMonthLabel }}
                        </span>

                        @if($nextMonth)
                            <a class="dasboard-iconbox-a"
                               href="{{ route('backend.tripDistance.index', ['all_courses' => $showAll ? 1 : 0, 'month' => $nextMonth['month'], 'year' => $nextMonth['year'], 'nav' => 1]) }}"
                               title="Ein Monat vor"
                               aria-label="Ein Monat vor">
                                <box-icon name='chevron-right'></box-icon>
                            </a>
                        @else
                            <span class="dasboard-iconbox-a opacity-40" aria-hidden="true">
                                <box-icon name='chevron-right'></box-icon>
                            </span>
                        @endif

                        @if($nextYear)
                            <a class="dasboard-iconbox-a"
                               href="{{ route('backend.tripDistance.index', ['all_courses' => $showAll ? 1 : 0, 'month' => $nextYear['month'], 'year' => $nextYear['year'], 'nav' => 1]) }}"
                               title="Ein Jahr vor"
                               aria-label="Ein Jahr vor">
                                <box-icon name='chevrons-right'></box-icon>
                            </a>
                        @else
                            <span class="dasboard-iconbox-a opacity-40" aria-hidden="true">
                                <box-icon name='chevrons-right'></box-icon>
                            </span>
                        @endif
                        </div>

                        <div class="mt-1 flex items-center">
                            @if($showAll)
                                <a class="dasboard-iconbox-a"
                                   href="{{ route('backend.tripDistance.index', ['all_courses' => 0, 'month' => $month, 'year' => $year, 'nav' => 1]) }}"
                                   title="Auf meine Termine wechseln"
                                   aria-label="Auf meine Termine wechseln">
                                    <box-icon name='user'></box-icon>
                                </a>
                            @else
                                <a class="dasboard-iconbox-a"
                                   href="{{ route('backend.tripDistance.index', ['all_courses' => 1, 'month' => $month, 'year' => $year, 'nav' => 1]) }}"
                                   title="Auf alle Termine wechseln"
                                   aria-label="Auf alle Termine wechseln">
                                    <box-icon name='calendar-event'></box-icon>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($coursedates->isEmpty())
            <p class="form-label">Keine Kurstermine für die aktuelle Auswahl gefunden.</p>
        @else
            <div class="dashboard-flexbox">
                @foreach($coursedates as $coursedate)
                    <div class="dashboard-flexbox-b1-2">
                        <div class="dashboard-flexbox-text">

                            <div class="dasboard-iconbox">
                                <a class="dasboard-iconbox-a"
                                   href="{{ route('backend.tripDistance.show', ['coursedate' => $coursedate->id, 'all_courses' => $showAll ? 1 : 0, 'month' => $month, 'year' => $year]) }}"
                                   title="Fahrtenbuch öffnen"
                                   aria-label="Fahrtenbuch öffnen">
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


