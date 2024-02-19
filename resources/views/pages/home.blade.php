@section('title' ,'Terminplan Kurse')

<x-frontend.layout>

<x-frontend.hero></x-frontend.hero>

<main id="main">

    @include('components.frontend.about');

    @include('components.frontend.services');

    @include('components.frontend.counts');

    {{--
    ToDo: Wird das noch benötigt?
    @include('components.frontend.team');
    --}}

</main><!-- End #main -->

</x-frontend.layout>
