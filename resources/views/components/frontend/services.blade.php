<!-- ======= Services Section ======= -->
<section id="services" class="services">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Termine</h2>
            <p>Es können aktuell {{ $countCoursedates }} Kurse gebucht werden.</p>
            <br>
            @if($organiser->terminInformation <> '')
              <p> {!! $organiser->terminInformation !!}</p>
            @endif
        </div>

        <div class="row">

            @foreach($coursedates as $coursedate)

            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                <div class="icon-box" data-aos="fade-up">
                    <a href="{{ route('frontend.course' , $coursedate->id)  }}"> <div class="icon"><i class="bx bx-calendar"></i></div></a>
                    <h4 class="title"><a href="{{ route('frontend.course' , $coursedate->id)  }}">{{ $coursedate->getCousename->kursName }} </a></h4>
                    <p class="description">
                        Kurs im Zeitfenster<br>
                        {{ date("d.m.Y", strtotime($coursedate->kursstarttermin)) }} {{ date("H:i", strtotime($coursedate->kursstarttermin)) }} Uhr<br>
                        {{ date("d.m.Y", strtotime($coursedate->kursendtermin)) }} {{ date("H:i", strtotime($coursedate->kursendtermin)) }} Uhr<br>
                        Kurslänge: {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)<br>
                        Trainer:
                        @foreach($coursedate->users as $user)
                            <br>
                            {{ $user->vorname }} {{ $user->nachname }}
                        @endforeach
                    </p>
                </div>
            </div>

            @endforeach

        </div>

    </div>
</section><!-- End Services Section -->
