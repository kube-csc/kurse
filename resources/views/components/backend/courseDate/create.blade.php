<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Course Date Create') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(isset($danger))
                <div class="alert alert-danger mb-5">
                    {{ $danger }}
                </div>
            @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <form action="{{ route('backend.courseDate.store') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group ">
                        <div class="form-card ">
                            <div class="form-field ">
                                <label for="kursstarttermin" class="form-label">Start Datum</label>
                                <input type="datetime-local" name="kursstarttermin" id="kursstarttermin" class="form-input" value="{{ $kursstarttermin }}">
                            </div>

                            <div class="form-field ">
                                <label for="kurslaenge" class="form-label">Kursdauer</label>
                                <input type="time" name="kurslaenge" id="kurslaenge"  class="form-input @if(isset($danger)) is-invalid @endif" value="{{ $kurslaenge }}">
                            </div>

                            <div class="form-field ">
                                <label for="kursendtermin" class="form-label">End Datum</label>
                                <input type="datetime-local" name="kursendtermin" id="kursendtermin" class="form-input @if(isset($danger)) is-invalid @endif" value="{{ $kursendtermin }}">
                            </div>

                            <div class="form-field ">
                                <label for="trainer_id" class="form-label">Trainer</label>
                                <div class="form-text">{{ Auth::user()->vorname }} {{ Auth::user()->vorname }}</div>
                            </div>

                            <div class="form-field ">
                                <label for="course_id" class="form-label">Kursname</label>
                                <select name="course_id">
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}"  @selected(old('couse_id') ?? $course->id  == $course_id)>
                                            {{ $course->kursName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-field ">
                                <label for="sportgeraetanzahl" class="form-label">Anzahl Sportgeräte</label>
                                <select name="sportgeraetanzahl">
                                    <option value="0"  @selected(old('sportgeraetanzahl') ?? 0 == $sportgeraetanzahl)>
                                        alle Sportgeräte
                                    </option>
                                    @for($i = 1; $i < $sportgeraetanzahlMax; $i++)
                                        <option value="{{ $i }}"  @selected(old('sportgeraetanzahl') ?? $i == $sportgeraetanzahl)>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-300 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Eintragen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
