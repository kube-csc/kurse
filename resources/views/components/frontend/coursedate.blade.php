@section('title' ,'Kursdaten')

<main id="main">
    <!-- ======= Breadcrumbs Section ======= -->
    <section class="breadcrumbs">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center">
                <h2>Kursdaten</h2>
                <ol>
                    <li><a href="/">Home</a></li>
                    <li>Kursdaten</li>
                </ol>
            </div>
        </div>
    </section><!-- End Breadcrumbs Section -->

    <section class="inner-page">
        <div class="container">

            <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                <h2>{{ $coursedate->getCousename->kursName }}</h2>
                Kurs im Zeitfenster<br>
                {{ date("d.m.Y", strtotime($coursedate->kursstarttermin)) }} {{ date("H:i", strtotime($coursedate->kursstarttermin)) }} Uhr<br>
                {{ date("d.m.Y", strtotime($coursedate->kursendtermin)) }} {{ date("H:i", strtotime($coursedate->kursendtermin)) }} Uhr<br>
                Kurslänge: {{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunde(n)<br>
                Trainer:
                @foreach($coursedate->users as $user)
                    <br>
                    {{ $user->vorname }} {{ $user->nachname }}
                @endforeach
            </div>

            <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                Trainer: {!! $coursedate->getCousename->kursBeschreibung !!}
            </div>

        </div>

    </section><!-- End About Section -->
</main><!-- End #main -->
