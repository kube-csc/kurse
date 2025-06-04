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
          @foreach($coursedates as $coursedate)

            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
               <div class="icon-box" data-aos="fade-up">
                  <a href="{{ route('frontend.course' , $coursedate->id) }}">
                      @if(strtotime($coursedate->kursstarttermin) + (strtotime($coursedate->kurslaenge) - strtotime('00:00:00')) == strtotime($coursedate->kursendtermin)
                            && date('Y-m-d', strtotime($coursedate->kursstarttermin)) == date('Y-m-d', strtotime($coursedate->kursendtermin)))
                          <div class="icon"><i class="bx bx-calendar-event"></i></div>
                      @elseif(strtotime($coursedate->kursstarttermin) + (strtotime($coursedate->kurslaenge) - strtotime('00:00:00')) != strtotime($coursedate->kursendtermin)
                             && date('Y-m-d', strtotime($coursedate->kursstarttermin)) == date('Y-m-d', strtotime($coursedate->kursendtermin)))
                          <div class="icon"><i class="bx bx-calendar-edit"></i></div>
                     @elseif(date('Y-m-d', strtotime($coursedate->kursstarttermin)) != date('Y-m-d', strtotime($coursedate->kursendtermin)))
                          <div class="icon"><i class="bx bx-calendar"></i></div>
                     @endif
                  </a>
                  <h4 class="title"><a href="{{ route('frontend.course' , $coursedate->id) }}">{{ $coursedate->getCousename->kursName }}</a></h4>
                  @if($coursedate->training_id)
                    <b>{{ $coursedate->getSportSectionAbteilung() }}</b>
                  @endif
                  <p class="description">
                       <?php
                           if (!setlocale(LC_TIME, 'de_DE.UTF-8')) {
                               setlocale(LC_TIME, 'German_Germany.1252'); // Für Windows
                           }
                           $startDay = strftime('%a', strtotime($coursedate->kursstarttermin));
                           $endDay   = strftime('%a', strtotime($coursedate->kursendtermin));
                       ?>
                       @if(strtotime($coursedate->kursstarttermin) + (strtotime($coursedate->kurslaenge) - strtotime('00:00:00')) == strtotime($coursedate->kursendtermin)
                             && date('Y-m-d', strtotime($coursedate->kursstarttermin)) == date('Y-m-d', strtotime($coursedate->kursendtermin)))
                           <b>Datum:</b><br>
                           {{ $startDay }} {{ date('d.m.Y', strtotime($coursedate->kursstarttermin)) }}<br>
                           <b>Uhrzeit:</b><br>
                            von {{ date('H:i', strtotime($coursedate->kursstarttermin)) }} Uhr bis {{ date('H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                       @endif
                       @if(strtotime($coursedate->kursstarttermin) + (strtotime($coursedate->kurslaenge) - strtotime('00:00:00')) != strtotime($coursedate->kursendtermin)
                             && date('Y-m-d', strtotime($coursedate->kursstarttermin)) == date('Y-m-d', strtotime($coursedate->kursendtermin)))
                           <b>Datum:</b><br>
                           {{ $startDay }} {{ date('d.m.Y', strtotime($coursedate->kursstarttermin)) }}<br>
                           <b>Uhrzeit:</b><br>
                           von {{ date('H:i', strtotime($coursedate->kursstarttermin)) }} Uhr bis {{ date('H:i', strtotime($coursedate->kursendtermin)) }} Uhr<br>
                           <span class="text-info" style="font-size: 0.95em;">
                              <i class="bx bx-info-circle"></i>
                               Die Startuhrzeit kann beim Buchen individuell angepasst werden.
                           </span>
                       @endif
                       @if(date('Y-m-d', strtotime($coursedate->kursstarttermin)) != date('Y-m-d', strtotime($coursedate->kursendtermin)))
                           <b>Terminserie<br>
                           Start Datum:</b><br>
                           von {{ $startDay }} {{ date('d.m.Y', strtotime($coursedate->kursstarttermin)) }}<br>
                           <b>Uhrzeit:</b><br>
                           ab {{ date('H:i', strtotime($coursedate->kursstarttermin)) }} Uhr<br>
                           <b>End Datum:</b><br>
                           bis {{ $endDay }} {{ date('d.m.Y', strtotime($coursedate->kursendtermin)) }}
                       @endif
                          <br>
                          <b>Dauer:</b><br>
                          {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)<br>
                          <b>{{ $organiser->trainerUeberschrift }}:</b>
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
