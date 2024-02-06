<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('backend.Course Dates') }}
            </h2>
            <div class="bg-white ml-5 border-2 boarder border-black shadow-gray-950">
                <a href="{{ route('backend.CourseDate.create') }}">
                  <box-icon name='calendar-plus' ></box-icon>
                </a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <table class="min-w-full leading-normal">
                    <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Datum
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Kursdauer
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Trainer
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Kursname
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Sportgeräte
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Bearbeiten
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($coursedates as $coursedate)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ date('H:i', strtotime($coursedate->kurslaenge)) }} Stunden</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $coursedate->getTrainerName->vorname }} {{ $coursedate->getTrainerName->nachname }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $coursedate->getCousename->kursName }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    @if($coursedate->sportgeraetanzahl)
                                        ?? von {{ $coursedate->sportgeraetanzahl }}</p>
                                    @else
                                        ?? von alle
                                    @endif
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a href="{{ route('backend.CourseDate.edit', $coursedate->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <box-icon name='edit' ></box-icon>
                                </a>
                                <a href="{{ route('backend.CourseDate.destroy', $coursedate->id) }}" class="text-indigo-600 hover:text-indigo-900" onclick="return confirm('Wirklich den Kurs löschen?')">
                                    <box-icon name='trash' ></box-icon>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>


