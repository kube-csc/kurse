<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }} von {{ Auth::user()->vorname }} {{ Auth::user()->nachname }}
        </h2>
    </x-slot>

    <div class="main-box">
        <div class="box">
            <div class="dashboard-menu-box">
                <x-courseBooking.dashboard/>
            </div>
        </div>
    </div>

</x-app-layout>
