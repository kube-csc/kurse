<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">Aktivitaetsbericht</h2>
        <div class="dasboard-iconbox">
            <a class="dasboard-iconbox-a"
               href="{{ route('admin.dashboard') }}"
               title="Zurueck zum Dashboard"
               aria-label="Zurueck zum Dashboard">
                <box-icon name='arrow-back'></box-icon>
            </a>
        </div>
    </x-slot>

    <div class="main-box">
        <div class="dashboard-flexbox">
            <div class="dashboard-flexbox-b1-2">
                <div class="dashboard-flexbox-text">
                    <div class="dasboard-iconbox">
                        <div class="flex items-center gap-2 flex-wrap">
                        @if($prevYear)
                            <a class="dasboard-iconbox-a"
                               href="{{ route('backend.tripDistance.report', ['all_courses' => $showAll ? 1 : 0, 'month' => $prevYear['month'], 'year' => $prevYear['year'], 'nav' => 1]) }}"
                               title="Ein Jahr zurueck"
                               aria-label="Ein Jahr zurueck">
                                <box-icon name='chevrons-left'></box-icon>
                            </a>
                        @else
                            <span class="dasboard-iconbox-a opacity-40" aria-hidden="true">
                                <box-icon name='chevrons-left'></box-icon>
                            </span>
                        @endif

                        @if($prevMonth)
                            <a class="dasboard-iconbox-a"
                               href="{{ route('backend.tripDistance.report', ['all_courses' => $showAll ? 1 : 0, 'month' => $prevMonth['month'], 'year' => $prevMonth['year'], 'nav' => 1]) }}"
                               title="Ein Monat zurueck"
                               aria-label="Ein Monat zurueck">
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
                               href="{{ route('backend.tripDistance.report', ['all_courses' => $showAll ? 1 : 0, 'month' => $nextMonth['month'], 'year' => $nextMonth['year'], 'nav' => 1]) }}"
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
                               href="{{ route('backend.tripDistance.report', ['all_courses' => $showAll ? 1 : 0, 'month' => $nextYear['month'], 'year' => $nextYear['year'], 'nav' => 1]) }}"
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
                                   href="{{ route('backend.tripDistance.report', ['all_courses' => 0, 'month' => $month, 'year' => $year, 'nav' => 1]) }}"
                                   title="Auf meine Termine wechseln"
                                   aria-label="Auf meine Termine wechseln">
                                    <box-icon name='user'></box-icon>
                                </a>
                            @else
                                <a class="dasboard-iconbox-a"
                                   href="{{ route('backend.tripDistance.report', ['all_courses' => 1, 'month' => $month, 'year' => $year, 'nav' => 1]) }}"
                                   title="Auf alle Termine wechseln"
                                   aria-label="Auf alle Termine wechseln">
                                    <box-icon name='calendar-event'></box-icon>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-flexbox-b1-2">
                <div class="dashboard-flexbox-text">
                    <h2 class="dasboard-iconbox-h2">Einstellungen</h2>
                    <div class="flex items-center gap-3">
                        <a class="inline-flex"
                           href="{{ route('backend.tripDistance.index', ['all_courses' => $showAll ? 1 : 0, 'month' => $month, 'year' => $year, 'nav' => 1]) }}"
                           title="Fahrtenbuch zur Distanzpflege"
                           aria-label="Fahrtenbuch zur Distanzpflege">
                            <img src="{{ asset('assets/img/fahrtenbuch.png') }}"
                                 alt="Fahrtenbuch"
                                 class="w-14 h-14 md:w-20 md:h-20 object-contain"
                                 title="Fahrtenbuch"
                                 aria-label="Fahrtenbuch">
                        </a>
                        <div>
                            <div class="text-sm text-gray-700 dark:text-gray-200">Zeitraum: {{ $currentMonthLabel }}</div>
                            <div class="text-sm text-gray-700 dark:text-gray-200">Jahr: {{ $year }}</div>
                            <div class="text-sm text-gray-700 dark:text-gray-200">Filter: {{ $showAll ? 'alle Kurse' : 'meine Kurse' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-flexbox mt-4">
            <div class="dashboard-flexbox-b1-2">
                <div class="dashboard-flexbox-text">
                    <h2 class="dasboard-iconbox-h2">Monatsauswertung Trainer</h2>
                    @if(($monthlyStats['trip_count'] ?? 0) === 0)
                        <p class="form-label">Keine Fahrleistung im gewaehlten Zeitraum vorhanden.</p>
                    @else
                        <p><label class="label">Anzahl Fahrten:</label> {{ $monthlyStats['trip_count'] }}</p>
                        <p><label class="label">Beteiligte Trainer:</label> {{ $monthlyStats['trainer_count'] }}</p>
                        <p><label class="label">Gesamtdistanz:</label> {{ number_format((float) $monthlyStats['total_distance'], 2, ',', '') }} km</p>

                        <div class="mt-3 space-y-1">
                            @foreach($monthlyStats['trainers'] as $trainer)
                                <div class="text-sm text-gray-700 dark:text-gray-200">
                                    {{ $trainer['name'] ?: ('Trainer #' . $trainer['id']) }}:
                                    {{ number_format((float) $trainer['distance'], 2, ',', '') }} km
                                    ({{ $trainer['rides'] }} Fahrten)
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="dashboard-flexbox-b1-2">
                <div class="dashboard-flexbox-text">
                    <h2 class="dasboard-iconbox-h2">Jahresstatistik {{ $year }}</h2>
                    <p><label class="label">Anzahl Fahrten:</label> {{ $yearlyStats['trip_count'] }}</p>
                    <p><label class="label">Jahresleistung:</label> {{ number_format((float) $yearlyStats['yearly_distance'], 2, ',', '') }} km</p>
                    <p><label class="label">Gesamtdistanz im Jahr:</label> {{ number_format((float) $yearlyStats['total_distance'], 2, ',', '') }} km</p>
                    <p><label class="label">Beteiligte Trainer:</label> {{ $yearlyStats['trainer_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="dashboard-flexbox mt-4">
            <div class="dashboard-flexbox-b1-2">
                <div class="dashboard-flexbox-text">
                    <h2 class="dasboard-iconbox-h2">Fahrten im Jahr</h2>
                    @if(empty($yearlyStats['trips']))
                        <div class="text-sm text-gray-600 dark:text-gray-300">Keine Fahrten mit Trainer-km vorhanden.</div>
                    @else
                        <div class="space-y-1">
                            @foreach($yearlyStats['trips'] as $trip)
                                <div class="text-sm text-gray-700 dark:text-gray-200">
                                    {{ $trip['date'] }} | {{ $trip['course'] }} | {{ number_format((float) $trip['distance'], 2, ',', '') }} km
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

