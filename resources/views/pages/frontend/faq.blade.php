@section('title' ,'FAQ')

<x-frontend.layout>

    <main id="main">
        @include('components.frontend.faq', ['groups' => $groups])
    </main><!-- End #main -->

</x-frontend.layout>

