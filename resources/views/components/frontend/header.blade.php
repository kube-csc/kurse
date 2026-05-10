@include('layouts.header')
<!-- ======= Header ======= -->
<header id="header" class="fixed-top header-transparent">
    <div class="container d-flex align-items-center">

        <div class="logo mr-auto">
            @if(session('is_iframe_mode'))
                <h1 class="text-light"><span>{{ $_SERVER['HTTP_HOST'] }}</span></h1>
            @else
                <h1 class="text-light"><a href="{{ request()->getSchemeAndHttpHost() }}"><span>{{ $_SERVER['HTTP_HOST'] }}</span></a></h1>
            @endif
            <!-- Uncomment below if you prefer to use an image logo -->
            <!-- <a href="index.html"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->
        </div>

        @php
            $host = $_SERVER['HTTP_HOST'] ?? null;
            $currentOrganiser = \App\Models\Organiser::where('veranstaltungDomain', $host)->first() ?? \App\Models\Organiser::find(1);
            $hasFaq = \App\Models\Faq::query()
                ->active()
                ->visibleForOrganiser($currentOrganiser?->id)
                ->exists();
        @endphp

        <nav class="nav-menu d-none d-lg-block">
            <ul>
                @if(!session('is_iframe_mode'))
                    <li class="active"><a href="{{ request()->getSchemeAndHttpHost() }}">Home</a></li>
                @endif

                @if($hasFaq)
                    <li><a href="{{ route('frontend.faq') }}">FAQ</a></li>
                @endif

                @if(Auth::check())
                    <li><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><a href="{{ route('frontend.logout') }}">{{ __('main.Log Out') }}</a></li>
                @else
                    <li><a href="/login">{{ __('main.Booking') }}</a></li>
                    <li><a href="/register">{{ __('main.Register') }}</a></li>
                @endif
            </ul>
        </nav><!-- .nav-menu -->

    </div>
</header><!-- End Header -->
