<div id="course-embed-container">
    <style>
        html, body {
            margin: 0;
            background-color: #f8fafc;
        }
        #course-embed-container {
            font-size: 0.75rem;
            background-color: #f8fafc;
            padding: 0.75rem;
            border-radius: 0.5rem;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .course-embed-card {
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            font-family: sans-serif;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }
        .course-embed-header {
            font-weight: bold;
            font-size: 1.00rem;
            margin-bottom: 0.5rem;
        }
        .course-embed-label {
            font-weight: 600;
            color: #4a5568;
        }
        .course-embed-button {
            display: inline-block;
            background-color: #4a5568;
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            text-decoration: none;
            margin-top: 0.5rem;
        }
        .course-embed-button:hover {
            background-color: #2d3748;
        }
    </style>

    @php
        if (!setlocale(LC_TIME, 'de_DE.UTF-8')) {
            setlocale(LC_TIME, 'German_Germany.1252');
        }
    @endphp
    @if($showDebug)
        <div style="background:#fff7ed; border:1px solid #fdba74; color:#9a3412; padding:0.5rem; border-radius:0.375rem; margin-bottom:0.75rem; font-family:monospace; font-size:0.8rem;">
            <strong>Debug URL:</strong> {{ $debugUrl }}<br>
            <strong>Kurs-Filter (course_ids):</strong>
            @if(!empty($filterCourseIds))
                {{ implode(', ', $filterCourseIds) }}
            @else
                alle Kurse (kein Filter)
            @endif
            <br>
            <strong>Gefundene Termine:</strong> {{ $coursedates->count() }}
        </div>
    @endif
    @if($isCourseParticipantLoggedIn)
        <h3>Willkommen, {{ Auth::user()->vorname }}</h3>
        <p>Hier sind die verfügbaren Kurse:</p>
    @else
        <div style="background: #edf2f7; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            <p>Um einen Kurs zu buchen, logge dich bitte ein oder erstelle einen neuen Account.</p>
            @php
                $loginUrl = route('login');
                $registerUrl = route('register');
                if (!empty($filterCourseIds)) {
                    $queryString = '?course_ids=' . implode(',', $filterCourseIds);
                    $loginUrl .= $queryString;
                    $registerUrl .= $queryString;
                }
            @endphp
            <a href="{{ $loginUrl }}" class="course-embed-button">Login</a>
            <a href="{{ $registerUrl }}" class="course-embed-button">Registrieren</a>
        </div>
    @endif

    <div class="course-list">

        @foreach($coursedates as $coursedate)
            @php
                $startDay = strftime('%a', strtotime($coursedate->kursstarttermin));
                $endDay   = strftime('%a', strtotime($coursedate->kursendtermin));
            @endphp
            <div class="course-embed-card">
                <div class="course-embed-header">{{ $coursedate->getCousename->kursName }}</div>

                <div>
                    <span class="course-embed-label">Termin:</span>
                    {{ $startDay }} {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }} Uhr
                    bis {{ $endDay }} {{ date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                </div>

                <div>
                    <span class="course-embed-label">Dauer:</span>
                    {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)
                </div>

                <div>
                    <span class="course-embed-label">Trainer:</span>
                    @foreach($coursedate->users as $user)
                        {{ $user->vorname }} {{ $user->nachname }}@if(!$loop->last), @endif
                    @endforeach
                </div>

                <div>
                    <span class="course-embed-label">Plätze:</span>
                    @if($coursedate->sportgeraetanzahl > 0)
                        {{ $coursedate->booked_count }} von {{ $coursedate->sportgeraetanzahl }} belegt
                    @else
                        {{ $coursedate->booked_count }} Teilnehmer
                    @endif
                </div>


                @if($isCourseParticipantLoggedIn)
                    <a href="{{ route('courseBooking.course.edit', $coursedate->id) }}" class="course-embed-button">
                        @if($coursedate->bookedSelf_count > 0)
                            Buchung bearbeiten
                        @else
                            Kurs buchen
                        @endif
                    </a>
                @else
                    @php
                        $loginUrl = route('login');
                        if (!empty($filterCourseIds)) {
                            $loginUrl .= '?course_ids=' . implode(',', $filterCourseIds);
                        }
                    @endphp
                    <a href="{{ $loginUrl }}" class="course-embed-button">Zum Buchen einloggen</a>
                @endif
            </div>
        @endforeach
    </div>
    <script>
        // Falls wir in einem IFrame sind, verstecken wir Navigations-Elemente,
        // falls sie durch das Session-Flag noch nicht erfasst wurden oder
        // falls statisches HTML geladen wird.
        if (window.self !== window.top) {
            document.querySelectorAll('a[href="{{ request()->getSchemeAndHttpHost() }}"]').forEach(function(el) {
                el.style.display = 'none';
            });
            // Zusätzliche Logik für Links, die "Home" oder "Logo" enthalten könnten
            document.querySelectorAll('.logo, .nav-menu li:first-child').forEach(function(el) {
                // Nur wenn sie den Host-Link enthalten
                if (el.querySelector('a[href="{{ request()->getSchemeAndHttpHost() }}"]')) {
                    el.style.display = 'none';
                }
            });
        }
    </script>
</div>
