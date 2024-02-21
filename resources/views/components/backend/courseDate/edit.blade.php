<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Course Dates') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.Course Date Create Help') !!}
                </p>
            </div>
       </div>
    </x-slot>
    <div class="main-box">
        <!--Temp: Fehlermeldung anzeigen wird nicht benutz-->
        @if(isset($danger))
            <div class="alert alert-danger mb-5 mt-1">
                {{ $danger }}
            </div>
        @endif
        <div class="box">
            <form action="{{ route('backend.courseDate.update', $coursedate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card">
                        <div class="form-field">
                            <label for="kursstarttermin" class="form-label">Start Datum</label>
                            <div class="form-field flex">
                                <input type="date" name="kursstartterminDatum" id="kursstartterminDatum" class="form-input-date"
                                        @if(isset($kursstartterminDatum))
                                            value="{{ $kursstartterminDatum }}"
                                        @else
                                            value="{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d') }}"
                                        @endif
                                >
                                <input type="time" name="kursstartterminTime" id="kursstartterminTime" class="form-input-date"
                                       @if(isset($kursstartterminTime))
                                           value="{{ $kursstartterminTime }}"
                                       @else
                                           value="{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('H:i') }}"
                                       @endif
                                >
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="kurslaenge" class="form-label">Kursdauer</label>
                            <input type="time" name="kurslaenge" id="kurslaenge"  class="form-input-date @if(isset($danger)) is-invalid @endif"
                                    @if(isset($kurslaenge))
                                        value="{{ $kurslaenge }}"
                                    @else
                                        value="{{ $coursedate->kurslaenge }}"
                                    @endif
                            >
                        </div>

                        <div class="form-field">
                            <label for="kursendtermin" class="form-label">End Datum</label>
                            <div class="form-field flex">
                                <input type="date" name="kursendterminDatum" id="kursendterminDatum" class="form-input-date @if(isset($danger)) is-invalid @endif"
                                        @if(isset($kursendterminDatum))
                                            value="{{ $kursendterminDatum }}"
                                        @else
                                            value="{{ Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('Y-m-d') }}"
                                        @endif
                                >
                                <input type="time" name="kursendterminTime" id="kursendterminTime" class="form-input-date @if(isset($danger)) is-invalid @endif"
                                       @if(isset($kursendterminTime))
                                           value="{{ $kursendterminTime }}"
                                       @else
                                           value="{{ Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('H:i') }}"
                                    @endif
                                >
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="trainer_id" class="form-label">Trainer</label>
                            <div class="form-text">{{ $coursedate->getTrainerName->vorname }} {{ $coursedate->getTrainerName->nachname }}</div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">Kursname</label>
                            <select name="course_id">
                                <!-- Fixme: Kursname bei Edit Aufruf kein alter Wert -->
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        @if(isset($course_id))
                                            @if($course->id == $course_id) selected @endif
                                        @else
                                            @if($course->id == $coursedate->course_id) selected @endif
                                        @endif
                                        >
                                        {{ $course->kursName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="sportgeraetanzahl" class="form-label">Anzahl Sportgeräte {{ $coursedate->sportgeraetanzahl }}</label>
                            <select name="sportgeraetanzahl">
                                <option value="0"  @selected(old('sportgeraetanzahl') ?? 0 == $coursedate->sportgeraetanzahl)>
                                    alle Sportgeräte
                                </option>
                                @for($i = 1; $i < $sportgeraetanzahlMax; $i++)
                                    <option value="{{ $i }}"
                                            @if(isset($kursendtermin))
                                                @selected(old('sportgeraetanzahl') ?? $i == $sportgeraetanzahl)
                                            @else
                                                @selected(old('sportgeraetanzahl') ?? $i == $coursedate->sportgeraetanzahl)
                                           @endif
                                    >
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-footer">
                    <a href="{{ route('backend.courseDate.index') }}" class="form-button">
                        Zurück
                    </a>
                    <button type="submit" class="form-button">
                        Eintragen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
