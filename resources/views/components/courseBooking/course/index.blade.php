<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
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
                                    <box-icon name='bookmark'></box-icon>
                                 @else
                                    <box-icon name='bookmark-plus'></box-icon>
                                 @endif
                            </a>
                        </div>
                        <label class="form-label">{{ $organiser->veranstaltung}} im Zeitfenster:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                        <label class="form-label">letzter möglicher {{ $organiser->veranstaltung }} Ende:</label>
                        {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                        <label class="form-label">Länge des Kurses:</label>
                        {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)
                        <label class="form-label">Name des Kurses:</label>
                        {{ $coursedate->getCousename->kursName }}<br>
                        <label class="form-label">{{ $organiser->trainerUeberschrift }}:</label>
                        @foreach($coursedate->users as $user)
                            {{ $user->vorname }} {{ $user->nachname }}<br>
                        @endforeach

                        @if($coursedate->sportgeraetanzahl > 0)
                            <div>
                              <label class="form-label">Teilnehmer:</label>
                                {{ $coursedate->booked_count }} von {{ $coursedate->sportgeraetanzahl }} Teilnehmer
                            </div>
                        @else
                            <div>
                                <label class="form-label">Teilnehmer:</label>
                                {{ $coursedate->booked_count }} Teilnehmer
                            </div>
                        @endif
                        <div>
                            <label class="form-label">Deine Buchungen:</label>
                             {{ $coursedate->bookedSelf_count }} von dir gebuchter Teilnehmer
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

