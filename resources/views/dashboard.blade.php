<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
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
