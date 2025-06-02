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
            <a href="{{ url('/') }}" class="dasboard-iconbox-a" target="_blank"><box-icon name='globe'></box-icon></a>
    </div>
</div>
