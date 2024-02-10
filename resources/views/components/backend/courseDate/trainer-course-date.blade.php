<div class="dashboard-flexbox">
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Deine aktuellen eingestellten Kurse</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.index') }}">
                  <box-icon name='calendar'></box-icon>
                </a>
                <a class="dasboard-iconbox-a" href="{{ route('backend.courseDate.create') }}">
                    <box-icon name='calendar-plus'></box-icon>
                </a>
            </div>
            <br>
              Du hast {{ $courseDateCount }} Kurs(e) eingestellt.
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">Alle deine Kurse</h2>
            Du hast {{ $courseDateCountAll }} Kurs(e) insgesamt eingestellt.
        </div>
    </div>
</div>