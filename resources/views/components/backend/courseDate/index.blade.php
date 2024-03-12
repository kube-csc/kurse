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
    <div class="main-box">
        <div class="dashboard-flexbox">
            @foreach($coursedates as $coursedate)
                @php($userIsInCourse = false)
                @foreach($coursedate->users as $user)
                    @if($user->id == Auth::user()->id)
                        @php($userIsInCourse = true)
                    @endif
                @endforeach
                @if($userIsInCourse == true)
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="dasboard-iconbox">
                            <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.edit', $coursedate->id) }}">
                                <box-icon name='calendar-edit'></box-icon>
                            </a>
                            <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.sportingEquipment', $coursedate->id) }}">
                                <box-icon name='user'></box-icon>
                            </a>
                            <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.destroy', $coursedate->id) }}" onclick="return confirm('Wirklich den Kurs vam {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr löschen?')">
                                <box-icon name='trash'></box-icon>
                            </a>
                        @if($coursedate->users->count() > 1)
                            <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.trainerDestroy', $coursedate->id) }}">
                                <box-icon name='minus'></box-icon>
                            </a>
                        @endif
                        {{ $coursedate->coursedate_id }}
                        </div>
                        <label class="form-label">Start Datum:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                        <label class="form-label">End Datum:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                        <label class="form-label">Länge des Kurses:</label>
                        {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)
                        <label class="form-label">Name des Kurses:</label>
                        {{ $coursedate->getCousename->kursName }}<br>
                        <label class="form-label">{{ $organiser->trainerUeberschrift }}:</label>
                        @foreach($coursedate->users as $user)
                            {{ $user->vorname }} {{ $user->nachname }}<br>
                        @endforeach
                         <div>
                            @if($coursedate->sportgeraetanzahl)
                                {{ $coursedate->sportgeraetanzahl }} Teilnehmer
                            @else
                                alle möglichen Teilnehmer
                            @endif
                         </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>


