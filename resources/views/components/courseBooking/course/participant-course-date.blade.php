<div class="dashboard-flexbox">
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">zu buchende Kurse</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('courseBooking.course.index') }}">
                  <box-icon name='calendar-event'></box-icon>
                 </a>
            </div>
        </div>
    </div>
    <div class="dashboard-flexbox-b1-2">
        <div class="dashboard-flexbox-text">
            <h2 class="dasboard-iconbox-h2">gebuchte Kurse</h2>
            <div class="dasboard-iconbox">
                <a class="dasboard-iconbox-a" href="{{ route('courseBooking.course.indexParticipant') }}">
                    <box-icon name='calendar-event'></box-icon>
                </a>
            </div>
        </div>
    </div>
</div>

