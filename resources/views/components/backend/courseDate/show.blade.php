<!--
Es wird eine Editor mit Tailwind gestylt benötigt. Folgende migration soll verwendet werden:
coursedate.table. Folgende Felder sollen in der folgenden Reihenfolge verwendet werden:
kursstarttermin mit dem format "dd.mm.YYYY h:i"
kurslaenge mit dem format "h:i"
mit trainer_id den Nachnamen und Vornamen  des Trainers ausgeben mit hilde von getTrainerName. Der Titel ist Trainer
mit  Course_id den Kursnamen mit hilfe von getCousename ausgeben. Der Titel ist Kursname.

Programmiere den den CoursedateController die edit function und die show function.

Schreine die Route in der web.php. Die Route ist backend.CourseDate.index und backend.CourseDate.show.
-->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Course Dates') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <form action="{{ route('backend.courseDate.update', $coursedate->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="kursstarttermin" class="block text-sm font-medium text-gray-700">Datum</label>
                                <input type="text" name="kursstarttermin" id="kursstarttermin" autocomplete="given-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }}">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="kurslaenge" class="block text-sm font-medium text-gray-700">Kursdauer</label>
                                <input type="text" name="kurslaenge" id="kurslaenge" autocomplete="family-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ gmdate('H:i', $coursedate->kurslaenge) }}">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="trainer_id" class="block text-sm font-medium text-gray-700">Trainer</label>
                                <input type="text" name="trainer_id" id="trainer_id" autocomplete="family-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ $coursedate->getTrainerName->vorname }} {{ $coursedate->getTrainerName->nachname }}">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="course_id" class="block text-sm font-medium text-gray-700">Kursname</label>
                                <input type="text" name="course_id" id="course_id" autocomplete="family-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ $coursedate->getCousename->kursName }}">
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
