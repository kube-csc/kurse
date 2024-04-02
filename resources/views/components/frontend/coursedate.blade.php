@section('title' ,'Termin')

<main id="main">
    <!-- ======= Breadcrumbs Section ======= -->
    <section class="breadcrumbs">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center">
                <h2>{{ $organiser->veranstaltung }}</h2>
                <ol>
                    <li><a href="/">Home</a></li>
                    <li>Termin</li>
                </ol>
            </div>
        </div>
    </section><!-- End Breadcrumbs Section -->

    <section class="inner-page">
        <div class="container">

            <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                <h2>{{ $coursedate->getCousename->kursName }}</h2>
                Termin im Zeitfenster<br>
                {{ date("d.m.Y", strtotime($coursedate->kursstarttermin)) }} {{ date("H:i", strtotime($coursedate->kursstarttermin)) }} Uhr<br>
                {{ date("d.m.Y", strtotime($coursedate->kursendtermin)) }} {{ date("H:i", strtotime($coursedate->kursendtermin)) }} Uhr<br>
                Dauer: {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)<br>
                Teilnehmer: {{ $teilnehmerKursBookeds }} von {{ $sportgeraetanzahlMax }}<br>
                <br><br>
                @if(Auth::check())
                    @if (Auth::user()->getTable()=='course_participants')
                      Deine gebuchten Teilnehmer: {{ $courseBookedCount }}
                      @if($teilnehmerKursBookeds < $sportgeraetanzahlMax and ($coursedate->kursstarttermin <> $coursedate->kursstartvorschlag or $coursedate->kursendtermin <> $coursedate->kursendvorschlag))
                          <a href="{{ route('courseBooking.course.book' , $coursedate->id) }}"><i class="bx bx-user-plus"></i> Teilnehmer buchen</a>
                          <br><br>
                      @endif
                      @if($teilnehmerKursBookeds < $sportgeraetanzahlMax)
                           <a href="{{ route('courseBooking.course.edit' , $coursedate->id) }}">
                             @if($courseBookedCount == 0)
                               <i class="bx bx-book-add"></i>
                            @else
                               <i class="bx bx-book"></i>
                            @endif
                               zur Terminbuchung
                           </a>
                           <br><br>
                      @endif
                    @endif
                @endif

                <h3>{{ $organiser->trainerUeberschrift }}:</h3>
                @foreach($coursedate->users as $user)
                    {{ $user->vorname }} {{ $user->nachname }}<br>
                @endforeach
            </div>

            @if($coursedate->kursInformation != null)
            <div class="section-title" data-aos="fade-in" data-aos-delay="150">
                <h3>Termin Information:</h3>
                {!! $coursedate->kursInformation !!}
            </div>
            @endif

            @if($coursedate->getCousename->kursBeschreibung != null)
            <div class="section-title" data-aos="fade-in" data-aos-delay="200">
                <h3>Information:</h3>
                {!! $coursedate->getCousename->kursBeschreibung !!}
            </div>
            @endif

            @if($sportEquipments->count() > 0)
                <div class="section-title" data-aos="fade-in" data-aos-delay="250">
                    <h3>{{ $organiser->materialUeberschrift }}:</h3>
                    <div class="row">
                        @foreach($sportEquipments as $sportEquipment)

                            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                                <div class="icon-box" data-aos="fade-up">
                                    <h4 class="title">{{ $sportEquipment->sportgeraet }}</h4>
                                    @if($sportEquipment->bild != Null)
                                        @if (!is_file('/storage/sportgeraet/'.$sportEquipment->bild))
                                            <img src="/storage/sportgeraet/{{ $sportEquipment->bild }}" width="100%" alt="{{ $sportEquipment->sportgeraet }}"/>
                                        @else
                                            @auth
                                                <p class="text-danger">Bild {{ $sportEquipment->bild }} ist nicht auf dem Server vorhanden.</p>
                                            @endauth
                                        @endif
                                    @endif
                                    @if($sportEquipment->typ != Null)
                                        <p class="description">
                                            {!! $sportEquipment->typ !!}
                                        </p>
                                    @endif

                                    @if($sportEquipment->laenge > 0 || $sportEquipment->breite > 0 || $sportEquipment->hoehe > 0)
                                        <p class="description">
                                            Masse:
                                            @if($sportEquipment->laenge > 0)
                                                L: {{ $sportEquipment->laenge }} m<br>
                                            @endif
                                            @if($sportEquipment->breite > 0)
                                                B: {{ $sportEquipment->breite }} m<br>
                                            @endif
                                            @if($sportEquipment->hoehe > 0)
                                                H: {{ $sportEquipment->hoehe }} m<br>
                                            @endif
                                        </p>
                                    @endif
                                    <p class="description">
                                        @if($sportEquipment->tragkraft != Null && $sportEquipment->tragkraft != 0)
                                            Tragkraft: {{ $sportEquipment->tragkraft }} kg<br>
                                        @endif
                                        @if($sportEquipment->gewicht != Null && $sportEquipment->gewicht != 0)
                                            Gewicht: {{ $sportEquipment->volumen }} kg<br>
                                        @endif
                                    </p>
                                </div>
                            </div>

                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </section><!-- End About Section -->
</main><!-- End #main -->
