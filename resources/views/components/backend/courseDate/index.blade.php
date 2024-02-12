<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                @if(Route::currentRouteNamed('backend.courseDate.index'))
                    {{ __('backend.Course Dates') }}
                @else
                    {{ __('backend.Course Dates All') }}
                @endif
            </h2>
            <div class="dasboard-iconbox w-12 ml-4">
                <a href="{{ route('backend.courseDate.create') }}">
                    <box-icon name='calendar-plus'></box-icon>
                </a>
            </div>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
        <div class="dashboard-flexbox">
            @foreach($coursedates as $coursedate)
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="dasboard-iconbox">
                            <a href="{{ route('backend.courseDate.edit', $coursedate->id) }}">
                                <box-icon name='edit'></box-icon>
                            </a>
                            <a href="{{ route('backend.courseDate.destroy', $coursedate->id) }}" onclick="return confirm('Wirklich den Kurs vam {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr löschen?')">
                                <box-icon name='trash'></box-icon>
                            </a>
                        </div>
                        <label class="form-label">Datum:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                        <label class="form-label">End Datum:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                        <label class="form-label">Kurslänge:</label>
                        {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)
                        <label class="form-label">Kursname:</label>
                        {{ $coursedate->getCousename->kursName }}<br>
                        <label class="form-label">Kursleiter:</label>
                        {{ $coursedate->getTrainerName->vorname }} {{ $coursedate->getTrainerName->nachname }}<br>
                        <div>
                            @if($coursedate->sportgeraetanzahl)
                                ?? von {{ $coursedate->sportgeraetanzahl }}
                            @else
                                ?? von alle verfügbaren
                            @endif
                            Sportgeräte
                        </div>
                        <!-- ToDo: Sportgeräte anzeigen -->
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>


