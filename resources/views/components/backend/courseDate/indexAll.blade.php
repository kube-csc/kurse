<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                {{ __('backend.Course Dates All') }}
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
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="dasboard-iconbox">
                            @if($coursedate->booked_count==0)
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.edit', $coursedate->id) }}">
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
                            @endif
                            @php($userIsInCourse = false)
                            @foreach($coursedate->users as $user)
                                @if($user->id == Auth::user()->id)
                                    @php($userIsInCourse = true)
                                @endif
                            @endforeach
                            @if($userIsInCourse == false)
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.trainerRegister', $coursedate->id) }}">
                                    <box-icon name='plus'></box-icon>
                                </a>
                            @else
                                @if($coursedate->users->count() > 1)
                                    <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.trainerDestroy', $coursedate->id) }}">
                                        <box-icon name='minus'></box-icon>
                                    </a>
                                @endif
                            @endif
                            @if($userIsInCourse == true && $coursedate->booked_count > 0)
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.CourseBockedInformation', $coursedate->id) }}">
                                    <box-icon name='info-square'></box-icon>
                                </a>
                            @endif
                            {{ $coursedate->coursedate_id }}
                        </div>
                        <label class="label">Name:</label>
                        {{ $coursedate->getCousename->kursName }}<br>
                        <label class="label">Start Datum:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                        <label class="label">letztmögliches Ende:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
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
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>


