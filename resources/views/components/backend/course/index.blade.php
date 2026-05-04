<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                    {{ __('backend.Course') }}
            </h2>
            <div class="dasboard-iconbox flex items-center ml-4">
                <a href="{{ route('backend.course.create') }}">
                    <box-icon name='calendar-plus' title="{{ __('Kurs erstellen') }}"></box-icon>
                </a>
                <a href="{{ route('backend.course.iframe_generator') }}" class="ml-2">
                    <box-icon name='code-block' title="{{ __('backend.IFrame Generator') }}"></box-icon>
                </a>
            </div>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
        <div class="dashboard-flexbox">
            @foreach($courses as $course)
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="flex justify-between items-start">
                            <div class="dasboard-iconbox">
                                <a href="{{ route('backend.course.edit', $course->id) }}">
                                    <box-icon name='edit'></box-icon>
                                </a>
                            </div>
                        </div>
                        <label class="label">Kurs / Training / Fahrt:</label>
                        {{ $course->kursName }}<br>
                        @if($course->kursBeschreibung != null)
                            <label class="label">Beschreibung:</label>
                            {!! $course->kursBeschreibung !!}<br>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>


