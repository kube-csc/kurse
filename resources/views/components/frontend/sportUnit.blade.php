<!-- ======= Services Section ======= -->
<section id="services" class="services">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Sportgeräte</h2>
            <p>Es stehen  {{ $countSportEquipments }} Sportgeräte für die Kurse zur Verfügung.</p>
        </div>

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
                                    L: {{ $sportEquipment->laenge }} m <br>
                                @endif
                                @if($sportEquipment->breite > 0)
                                    B: {{ $sportEquipment->breite }} m <br>
                                @endif
                                @if($sportEquipment->hoehe > 0)
                                    H: {{ $sportEquipment->hoehe }} m <br>
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
</section><!-- End Services Section -->
