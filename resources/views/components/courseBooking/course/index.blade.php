<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                    {{ __('backend.Course Dates All') }}
             </h2>
        </div>
    </x-slot>
    <div class="main-box">
        <div class="dashboard-flexbox">
            @foreach($coursedates as $coursedate)
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="dasboard-iconbox">
                            <a class="dasboard-iconbox-a" href="{{ route('courseBooking.course.edit', $coursedate->id) }}">
                                 @if($coursedate->bookedSelf_count > 0)
                                    <box-icon name='bookmark'></box-icon>Buchungen bearbeiten
                                 @else
                                    <box-icon name='bookmark-plus'></box-icon>Termin buchen
                                 @endif
                            </a>
                        </div>
                        <label class="label">Name:</label>
                        {{ $coursedate->getCousename->kursName }}<br>
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
                            <label class="label">Serientermin von:</label>
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
                                {{ $coursedate->booked_count }} Teilnehmer
                            </div>
                        @endif
                        <div>
                            <label class="label">Deine Buchungen:</label>
                             {{ $coursedate->bookedSelf_count }} von dir gebuchte Teilnehmer
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>


