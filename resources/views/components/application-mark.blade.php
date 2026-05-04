<div class="dashboard-flexbox-text">
    <div class="dasboard-iconbox">
        @if(request()->is('admin*') || request()->is('backend*'))
            <a href="{{ route('admin.dashboard') }}">
                <box-icon name='home'></box-icon>
            </a>
        @else
            <a href="{{ route('dashboard') }}">
                <box-icon name='home'></box-icon>
            </a>
        @endif
        @if(!session('is_iframe_mode') || session('embed_origin_url'))
            @if(session('embed_origin_url'))
                <a href="{{ session('embed_origin_url') }}" class="dasboard-iconbox-a" target="_top"><box-icon name='globe'></box-icon></a>
            @else
                <a href="{{ request()->getSchemeAndHttpHost() }}" class="dasboard-iconbox-a" target="_blank"><box-icon name='globe'></box-icon></a>
            @endif
        @endif
    </div>
</div>
