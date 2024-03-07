<!-- ======= About Section ======= -->
<section id="about" class="about">
    <div class="container">

        <div class="row no-gutters">
            <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
                <div class="content">
                    @if($countCoursedates==0)
                        <p>{!! $organiser->keineKurse !!}</p>
                    @endif
                    <p>{!! $organiser->veranstaltungBeschreibungKurz !!}</p>
                    <a href="{{ route('frontend.offer') }}" class="about-btn">mehr Informationen <i class="bx bx-chevron-right"></i></a>
                </div>
            </div>
            <div class="col-xl-7 d-flex align-items-stretch">
                <div class="icon-boxes d-flex flex-column justify-content-center">
                    <div class="row">
                        <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
                            <a href="/Sportart"><i class="bx bx-info-circle"></i></a>
                            <h4>{{ $organiser->sportartUeberschrift }}</h4>
                            {!! $organiser->sportartBeschreibungKurz !!}
                            <div class="read-more">
                                <a href="/Sportart" class="icofont-arrow-right">mehr</a>
                            </div>
                        </div>
                        <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="200">
                            <a href="/Trainer"><i class="bx bx-info-circle"></i></a>
                            <h4>{{ $organiser->trainerUeberschrift }}</h4>
                            @foreach($trainers as $trainer)
                                <ul>
                                    <li>{{ $trainer->getKursTrainer->vorname }} {{ $trainer->getKursTrainer->nachname }}</li>
                                </ul>
                            @endforeach
                            <div class="read-more">
                                <a href="/Trainer" class="icofont-arrow-right">mehr</a>
                            </div>
                        </div>
                        <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="300">
                            <a href="/Sportgeräte"><i class="bx bx-info-circle"></i></a>
                            @if($organiser->materialBeschreibungKurz<>'')
                                <p>{!! $organiser->materialBeschreibungKurz !!}</p>
                            @else
                            <h4>Sportgeräte</h4>
                            <ul>
                                @foreach($sportEquipments as $sportEquipment)
                                    <li>{{ $sportEquipment->sportgeraet }}</li>
                                @endforeach
                            </ul>
                            @endif
                            <div class="read-more">
                                <a href="/Sportgeräte" class="icofont-arrow-right">mehr</a>
                            </div>
                        </div>
                        <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="400">
                            <a href="/Kurse"><i class="bx bx-info-circle"></i></a>
                            <h4>Welche Kurse gibt es?</h4>
                            <ul>
                                @foreach($courses as $course)
                                    <li>{{ $course->kursName }}</li>
                                @endforeach
                            </ul>
                            <div class="read-more">
                                <a href="/Kurse" class="icofont-arrow-right">mehr</a>
                            </div>
                        </div>
                    </div>
                </div><!-- End .content-->
            </div>
        </div>

    </div>
</section><!-- End About Section -->
