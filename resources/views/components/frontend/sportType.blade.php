<!-- ======= Breadcrumbs Section ======= -->
<section class="breadcrumbs">
  <div class="container">

    <div class="d-flex justify-content-between align-items-center">
      <h2></h2>
      <a href="/#services">zu den Terminen</a>
      <ol>
        <li><a href="/">Home</a></li>
        <li>{{ $organiser->sportartUeberschrift }}</li>
      </ol>
    </div>
  </div>
</section><!-- End Breadcrumbs Section -->

<section class="inner-page">
  <div class="container">

      <div class="section-title" data-aos="fade-in" data-aos-delay="100">
          <div style="text-align: left;">
              {!! $organiser->getOrganiserInformation->sportartBeschreibungLang !!}
          </div>
      </div>

      <div class="section-title" data-aos="fade-in" data-aos-delay="150">

      </div>

  </div>

</section><!-- End About Section -->
