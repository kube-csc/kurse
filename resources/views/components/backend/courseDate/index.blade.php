<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                {{ __('backend.Course Dates') }}
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
            @php
                if (!setlocale(LC_TIME, 'de_DE.UTF-8')) {
                   setlocale(LC_TIME, 'German_Germany.1252'); // Für Windows
                }
            @endphp
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
                            @if($coursedate->booked_count==0)
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.edit', $coursedate->id) }}">
                                    <box-icon name='calendar-edit'></box-icon>
                                </a>
                            @else
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.editBooked', $coursedate->id) }}">
                                    <box-icon name='calendar-edit'></box-icon>
                                </a>
                            @endif
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.sportingEquipment', $coursedate->id) }}">
                                    <box-icon name='user'></box-icon>
                                </a>
                            @if($coursedate->booked_count==0)
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.destroy', $coursedate->id) }}" onclick="return confirm('Wirklich den Kurs vam {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr löschen?')">
                                    <box-icon name='trash'></box-icon>
                                </a>
                            @else
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.CourseBockedInformation', $coursedate->id) }}">
                                   <box-icon name='info-square'></box-icon>
                                </a>
                            @endif
                            @if($coursedate->users->count() > 1)
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.trainerDestroy', $coursedate->id) }}">
                                    <box-icon name='minus'></box-icon>
                                </a>
                           @endif
                        </div>
                        <label class="label">Name:</label>
                        {{ $coursedate->getCousename->kursName }}<br>
                        @if($coursedate->training_id)
                            <label class="label">{{ env('MENUE_ABTEILUNG') }} / {{ env('MENUE_MANNSCHAFTEN') }}:</label>
                            <b>{{ $coursedate->getSportSectionAbteilung() }}</b>
                        @endif
                        @if(strtotime($coursedate->kursstarttermin) + (strtotime($coursedate->kurslaenge) - strtotime('00:00:00')) == strtotime($coursedate->kursendtermin)
                              && date('Y-m-d', strtotime($coursedate->kursstarttermin)) == date('Y-m-d', strtotime($coursedate->kursendtermin)))
                            <label class="label">Termin von:</label>
                            {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                            <label class="label">bis:</label>
                            {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                        @endif
                        @if(strtotime($coursedate->kursstarttermin) + (strtotime($coursedate->kurslaenge) - strtotime('00:00:00')) != strtotime($coursedate->kursendtermin)
                              && date('Y-m-d', strtotime($coursedate->kursstarttermin)) == date('Y-m-d', strtotime($coursedate->kursendtermin)))
                            <label class="label">Termin im Zeitfenster:</label>
                            {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                            <label class="label">letztmögliches Ende des Termins:</label>
                            {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                        @endif
                        @if(date('Y-m-d', strtotime($coursedate->kursstarttermin)) != date('Y-m-d', strtotime($coursedate->kursendtermin)))
                            <label class="label">wiederkehrender Termin von:</label>
                            {{ date('d.m.Y', strtotime($coursedate->kursstarttermin)) }} ab {{ date('H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                            <label class="label">bis:</label>
                            {{ date('d.m.Y', strtotime($coursedate->kursendtermin)) }}
                        @endif
                        <label class="label">Dauer:</label>
                        {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)
                        <label class="label">{{ $organiser->trainerUeberschrift }}:</label>
                        @foreach($coursedate->users as $user)
                            {{ $user->vorname }} {{ $user->nachname }}<br>
                        @endforeach
                        @if($coursedate->sportgeraetanzahl > 0)
                            <div>
                                <label class="label">Teilnehmer:</label>
                                {{ $coursedate->booked_count }} von {{ $coursedate->sportgeraetanzahl }} Teilnehmer
                            </div>
                        @else
                            <div>
                                <label class="label">Teilnehmer:</label>
                                {{ $coursedate->booked_count }} von allen möglichen Teilnehmer
                            </div>
                        @endif
                        <label class="label">Termin ist im Terminangebot ausgeblendet:</label>
                        {{ $coursedate->kursNichtDurchfuerbar == 0 ? 'Nein' : 'Ja' }}
                        <label class="label">{{ $organiser->trainerUeberschrift }}:</label>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>


