<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                    {{ __('backend.Course Dates') }}
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
                                <box-icon name='bookmark'></box-icon> Buchungen bearbeiten
                            </a>
                        </div>
                        <label class="label">Name:</label>
                        {{ $coursedate->getCousename->kursName }}<br>
                        <label class="label">Termin im Zeitfenster:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                        <label class="label">letztmögliches Ende::</label>
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


