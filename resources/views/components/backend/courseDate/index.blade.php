<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                {{ __('backend.Course Dates') }}
            </h2>
            <div class="dasboard-iconbox ml-4">
                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.create') }}" title="Kurstermin erstellen" aria-label="Kurstermin erstellen">
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
                @php
                    $startDay = strftime('%a', strtotime($coursedate->kursstarttermin));
                    $endDay   = strftime('%a', strtotime($coursedate->kursendtermin));
                    $userIsInCourse = false;
                @endphp
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
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.edit', $coursedate->id) }}" title="Termin bearbeiten" aria-label="Termin bearbeiten">
                                    <box-icon name='calendar-edit'></box-icon>
                                </a>
                            @else
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.editBooked', $coursedate->id) }}" title="Termin bearbeiten" aria-label="Termin bearbeiten">
                                    <box-icon name='calendar-edit'></box-icon>
                                </a>
                            @endif
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.sportingEquipment', $coursedate->id) }}" title="Teilnehmer und Material verwalten" aria-label="Teilnehmer und Material verwalten">
                                    <box-icon name='user'></box-icon>
                                </a>
                                <a class="dasboard-iconbox-a"
                                   href="{{ route('backend.tripDistance.show', ['coursedate' => $coursedate->id, 'all_courses' => 0]) }}"
                                   title="Fahrtenbuch öffnen"
                                   aria-label="Fahrtenbuch öffnen">
                                    <box-icon name='line-chart'></box-icon>
                                </a>
                            @if($coursedate->booked_count==0)
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.destroy', $coursedate->id) }}" title="Termin löschen" aria-label="Termin löschen" onclick="return confirm('Wirklich den Kurs vam {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr löschen?')">
                                    <box-icon name='trash'></box-icon>
                                </a>
                            @else
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.CourseBockedInformation', $coursedate->id) }}" title="Buchungsinformationen anzeigen" aria-label="Buchungsinformationen anzeigen">
                                   <box-icon name='info-square'></box-icon>
                                </a>
                            @endif
                            @if($coursedate->users->count() > 1)
                                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.trainerDestroy', $coursedate->id) }}" title="Trainer vom Termin entfernen" aria-label="Trainer vom Termin entfernen">
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
                            {{ $startDay }} {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                            <label class="label">bis:</label>
                            {{ $endDay }} {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                        @endif
                        @if(strtotime($coursedate->kursstarttermin) + (strtotime($coursedate->kurslaenge) - strtotime('00:00:00')) != strtotime($coursedate->kursendtermin)
                              && date('Y-m-d', strtotime($coursedate->kursstarttermin)) == date('Y-m-d', strtotime($coursedate->kursendtermin)))
                            <label class="label">Termin im Zeitfenster:</label>
                            {{ $startDay }} {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                            <label class="label">letztmögliches Ende des Termins:</label>
                            {{ $endDay }} {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                        @endif
                        @if(date('Y-m-d', strtotime($coursedate->kursstarttermin)) != date('Y-m-d', strtotime($coursedate->kursendtermin)))
                            <label class="label">Serientermin von:</label>
                            {{ $startDay }} {{ date('d.m.Y', strtotime($coursedate->kursstarttermin)) }} ab {{ date('H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                            <label class="label">bis:</label>
                            {{ $endDay }} {{ date('d.m.Y', strtotime($coursedate->kursendtermin)) }}
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
                        <label class="label">Termin kann wegen zeitlicher Überschneidungen nicht angeboten werden:</label>
                        {{ $coursedate->kursNichtDurchfuerbar == 0 ? 'Nein' : 'Ja' }}
                        <label class="label">Von Buchungsangebot ausblenden:</label>
                        {{ $coursedate->getCousename->nicht_anmeldebar == 1 ? 'Ja' : 'Nein' }}
                        <label class="label">{{ $organiser->trainerUeberschrift }}:</label>
                        {{ $coursedate->getCousename->trainer == 1 ? 'Ja' : 'Nein' }}
                        <label class="label">Link zu Buchung:</label>
                        <button type="button" class="dasboard-iconbox-a" title="Link kopieren" aria-label="Link kopieren" onclick="copyBookingLink(@js('https://' . $organiser->veranstaltungDomain . '/Kurseangebot/' . $coursedate->id), this)">
                            <box-icon name='copy' size=xs'></box-icon>
                        </button>
                        <a href="https://{{ $organiser->veranstaltungDomain }}/Kurseangebot/{{ $coursedate->id }}" target="_blank" rel="noopener noreferrer">
                            https://{{ $organiser->veranstaltungDomain }}/Kurseangebot/{{ $coursedate->id }}
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    <script>
        function copyBookingLink(url, button) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(function () {
                    button.title = 'Kopiert';
                    setTimeout(function () {
                        button.title = 'Link kopieren';
                    }, 1200);
                });
                return;
            }

            var textArea = document.createElement('textarea');
            textArea.value = url;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            button.title = 'Kopiert';
            setTimeout(function () {
                button.title = 'Link kopieren';
            }, 1200);
        }
    </script>
</x-app-layout>


