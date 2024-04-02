<div class="dashboard-flexbox">
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Deine aktuell eingestellten Termin(e)</h2>
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
            <h2 class="dasboard-iconbox-h2">aktuell eingestellten Termin(e)</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.indexAll') }}">
                    <box-icon name='calendar-event'></box-icon>
                </a>
            </div>
            <br>
            Es sind {{ $courseDateCount }} Termin(e) eingestellt.
        </div>
    </div>
    {{--ToDo: Flexboxen  überprüfen sehen irgenwie unterschiedlich aus --}}
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Alle deine eingestellten Termin</h2>
            Du hast {{ $courseDateCountYouAll }} Termin(e) insgesamt im laufenden Jahr eingestellt.
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Alle eingestellten Termin(e)</h2>
            Es sind {{ $courseDateCountAll }} Termin(e) insgesamt im laufenden Jahr eingestellt worden.
        </div>
    </div>
</div>

