<!-- ======= Services Section ======= -->
<section id="services" class="services">
    <div class="container">

       <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Termine</h2>
            <p>Folgende Anzahl der {{ $organiser->veranstaltung }} stehen zur Zeit zur Verfügung: {{ $countCoursedates }}</p>
            <br>
            @if($organiser->getOrganiserInformation->terminInformation <> '')
              <p> {!! $organiser->getOrganiserInformation->terminInformation !!}</p>
            @endif
       </div>

       <div class="row">
          @php
            if (!setlocale(LC_TIME, 'de_DE.UTF-8')) {
               setlocale(LC_TIME, 'German_Germany.1252'); // Für Windows
            }
          @endphp
          @foreach($coursedates as $coursedate)

            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
               <div class="icon-box" data-aos="fade-up">
                  <a href="{{ route('frontend.course' , $coursedate->id) }}"> <div class="icon"><i class="bx bx-calendar-event"></i></div></a>
                  <h4 class="title"><a href="{{ route('frontend.course' , $coursedate->id) }}">{{ $coursedate->getCousename->kursName }}</a></h4>
                  @if($coursedate->training_id)
                      {{ $coursedate->training_id }} {{--Temp: Nur zum test --}}
                       <b>{{ $coursedate->getSportSectionAbteilung() }}</b>
                  @endif
                  <p class="description">
                      <?php
                        $startDay = strftime('%a', strtotime($coursedate->kursstarttermin));
                        $endDay   = strftime('%a', strtotime($coursedate->kursendtermin));
                      ?>
                      Termin im Zeitfenster<br>
                      {{ $startDay }} {{ date("d.m.Y", strtotime($coursedate->kursstarttermin)) }} {{ date("H:i", strtotime($coursedate->kursstarttermin)) }} Uhr<br>
                      {{ $endDay }} {{ date("d.m.Y", strtotime($coursedate->kursendtermin)) }} {{ date("H:i", strtotime($coursedate->kursendtermin)) }} Uhr<br>
                      Dauer: {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)<br>
                      Trainer:
                      @foreach($coursedate->users as $user)
                         <br>
                         {{ $user->vorname }} {{ $user->nachname }}
                      @endforeach
                  </p>
                  @auth
                  <div class="read-more">
                      <a href="{{ route('courseBooking.course.edit', $coursedate->id) }}" class="icofont-arrow-right">Termin {{ __('main.Booking') }} / stornieren</a>
                  </div>
                  @endauth
                  @guest
                  <div class="read-more">
                      <a href="/login" class="icofont-arrow-right">{{ __('main.Booking') }}</a><br>
                      <a href="/register" class="icofont-arrow-right">Registrieren</a>
                  </div>
                  @endguest
               </div>
            </div>
          @endforeach

       </div>
    </div>
</section><!-- End Services Section -->
