<!-- ======= Services Section ======= -->
<section id="services" class="services">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Termine</h2>
            <p>Es können aktuell {{ $countCoursdates }} Kurse gebucht werden.</p>
            <br>
            <p> @include('textimport.kurse') </p>
        </div>

        <div class="row">

            @foreach($coursdates as $coursdate)

            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                <div class="icon-box" data-aos="fade-up">
                    <a href="{{ route('frontend.course' , $coursdate->id)  }}"> <div class="icon"><i class="bx bx-calendar"></i></div></a>
                    <h4 class="title"><a href="">{{ $coursdate->getCousename->kursName }} </a></h4>
                    <p class="description">
                        Kurs im Zeitfenster<br>
                        {{ date("d.m.Y", strtotime($coursdate->kursstarttermin)) }} {{ date("H:i", strtotime($coursdate->kursstarttermin)) }} Uhr<br>
                        {{ date("d.m.Y", strtotime($coursdate->kursendtermin)) }} {{ date("H:i", strtotime($coursdate->kursendtermin)) }} Uhr<br>
                        Kurslänge: {{ date('H:i', strtotime($coursdate->kurslaenge)) }} Stunde(n)<br>
                        Trainer: {{ $coursdate->getTrainerName->vorname }} {{ $coursdate->getTrainerName->nachname }}
                    </p>
                </div>
            </div>

            @endforeach

        </div>

    </div>
</section><!-- End Services Section -->
