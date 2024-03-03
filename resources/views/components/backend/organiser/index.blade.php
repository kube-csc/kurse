<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('backend.Organiser') }}
            </h2>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
        <div class="dashboard-flexbox">
            @foreach($organisers as $organiser)
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="dasboard-iconbox">
                            <a href="{{ route('backend.organiser.edit', $organiser->id) }}">
                                <box-icon name='edit'></box-icon>
                            </a>
                        </div>
                        <label class="form-label">Veranstaltung:</label>
                        {{ $organiser->veranstaltung }}<br>
                        @if($organiser->veranstaltungDomain != null)
                            <label class="form-label">Domain der Veranstaltung:</label>
                            {!! $organiser->veranstaltungDomain!!}<br>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>


