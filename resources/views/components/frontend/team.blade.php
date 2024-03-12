<!-- ======= Team Section ======= -->
<section id="team" class="team">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>{{ $organiser->trainerUeberschrift }}</h2>
            <p>Wir haben {{ $countTrainers }} {{ $organiser->trainerUeberschrift }} für unsere {{ $organiser->veranstaltung }} zur Verfügung.</p>
        </div>

        <div class="row">

            @foreach ($trainers as $trainer)
            <div class="col-lg-4 col-md-6">
                <div class="member" data-aos="fade-up">
                    <div class="pic"></div>
                    <div class="member-info">
                        <h4>{{ $trainer->getKursTrainer->vorname }} {{ $trainer->getKursTrainer->nachname }}</h4>
                        <span>{{ $trainer->getTrainertyp->trainerfunktion }}</span>
                        <div class="social">
                           @php
                               $break="";
                           @endphp
                           @if($trainer->getKursTrainer->telefon<>'')
                               <a href=""><i class="icofont-telephone"></i>{{ $trainer->getKursTrainer->telefon }}</a>
                                @php
                                    $break="<br>";
                                @endphp
                            @endif
                           @if(isset($trainer->getKursTrainer->email) | $trainer->getKursTrainer->email != str_replace("@domain.de", "" , $trainer->getKursTrainer->email))
                               {!! $break !!}<a href=""><i class="icofont-mail"></i>{{ $trainer->getKursTrainer->email }}</a>
                           @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

    </div>
</section><!-- End Team Section -->
