<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                    {{ __('backend.Organiser') }}
            </h2>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
        <div class="dashboard-flexbox">
            @foreach($organisers as $organiser)
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="dasboard-iconbox" style="display:flex; gap:10px; align-items:center;">
                            <a href="{{ route('backend.organiser.edit', $organiser->id) }}" title="Veranstaltung bearbeiten">
                                <box-icon name='edit'></box-icon>
                            </a>

                            <a href="{{ route('faq.index', ['organiser' => $organiser->id]) }}" title="FAQ verwalten">
                                <box-icon name='help-circle'></box-icon>
                            </a>
                        </div>
                        <label class="label">Veranstaltung:</label>
                        {{ $organiser->veranstaltung }}<br>
                        @if($organiser->veranstaltungDomain != null)
                            <label class="label">Domain der Veranstaltung:</label>
                            {!! $organiser->veranstaltungDomain!!}<br>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
