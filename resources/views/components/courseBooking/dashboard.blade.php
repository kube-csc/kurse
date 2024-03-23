<div class="dashboard-flexbox">
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">zu buchende Termine</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('courseBooking.course.index') }}">
                    <box-icon name='calendar-event'></box-icon>
                </a>
            </div>
            <br>
            Es sind {{ $courseDateCount }} Termin(e) eingestellt.
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">gebuchte Termine</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('courseBooking.course.indexParticipant') }}">
                    <box-icon name='calendar-event'></box-icon>
                </a>
            </div>
            <br>
            {{ $courseParticipantCount }} Teilnehmer in {{ $courseDateCountYou }} gebuchten Termin(e).
        </div>
    </div>
</div>
