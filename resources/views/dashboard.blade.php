<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }} von {{ Auth::user()->vorname }} {{ Auth::user()->vorname }}
        </h2>
    </x-slot>

    <div class="main-box">
        <div class="box">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
                <x-backend.coursedate.trainer-course-date />
            </div>
        </div>
    </div>

    <div class="main-box">
        <div class="box">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
                <x-backend.sportEquipment.dashboard />
            </div>
        </div>
    </div>

    <div class="main-box">
        <div class="box">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
                <x-backend.course.dashboard />
            </div>
        </div>
    </div>

    <div class="main-box">
        <div class="box">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
                <x-backend.organiser.dashboard />
            </div>
        </div>
    </div>

</x-app-layout>
