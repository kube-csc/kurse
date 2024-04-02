<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                    {{ __('backend.Course') }}
            </h2>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
        <div class="dashboard-flexbox">
            @foreach($courses as $course)
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="dasboard-iconbox">
                            <a href="{{ route('backend.course.edit', $course->id) }}">
                                <box-icon name='edit'></box-icon>
                            </a>
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


