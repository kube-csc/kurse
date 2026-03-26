<div class="dashboard-flexbox">
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Deine aktuell eingestellten Termine</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.index') }}">
                  <box-icon name='calendar-event'></box-icon>
                 </a>
                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.create') }}">
                    <box-icon name='calendar-plus'></box-icon>
                </a>
            </div>
            <br>
              Du hast {{ $courseDateCountYou }} Termin(e) eingestellt.
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">aktuell eingestellte Termine</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.indexAll') }}">
                    <box-icon name='calendar-event'></box-icon>
                </a>
            </div>
            <br>
            Es sind {{ $courseDateCount }} Termin(e) eingestellt.
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Fahrtenbuch alle Termine</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('backend.tripDistance.index', ['all_courses' => 1]) }}">
                    <box-icon name='line-chart'></box-icon>
                </a>
            </div>
            <br>
            Kursdistanz pflegen und individuelle km für Teilnehmer sowie Trainer anpassen.
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Fahrtenbuch meine Termine</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('backend.tripDistance.index') }}">
                    <box-icon name='line-chart'></box-icon>
                </a>
            </div>
            <br>
            Kursdistanz pflegen und individuelle km für Teilnehmer sowie Trainer anpassen.
        </div>
    </div>
    {{--ToDo: Flexboxen  überprüfen sehen irgenwie unterschiedlich aus --}}
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Alle deine eingestellten Termine</h2>
            Du hast {{ $courseDateCountYouAll }} Termin(e) insgesamt im laufenden Jahr eingestellt.
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Alle eingestellten Termine</h2>
            Es sind {{ $courseDateCountAll }} Termin(e) insgesamt im laufenden Jahr eingestellt worden.
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Informationsmail versenden</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('backend.trainerMail') }}">
                    <box-icon name='envelope'></box-icon>
                </a>
            </div>
            <br>
            Es werden alle Kursleiter / Trainer per E-Mail benachrichtigt, bei denen der nächste Termin in den nächsten 2 Tagen ansteht.
        </div>
    </div>

    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Aktivitaetsbericht</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a"
                   href="{{ route('backend.tripDistance.index', ['all_courses' => 1]) }}"
                   title="Aktivitaetsbericht oeffnen"
                   aria-label="Aktivitaetsbericht oeffnen">
                    <box-icon name='bar-chart-alt-2'></box-icon>
                </a>
            </div>
            <br>
            Trainer-Fahrleistung und Jahresleistung als Visualisierung oeffnen.
        </div>
    </div>
</div>
