<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
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
        <div class="box">

            <form action="{{ route('backend.courseDate.update', $coursedate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card" x-data="{ kursstatterminDatum: '{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d') }}', kursendterminDatum: '{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d') }}' }">
                        <div class="form-field">
                            <label for="kursstarttermin" class="form-label">Start Datum:</label>
                            <div class="form-field flex">
                                <input type="date" name="kursstartterminDatum" id="kursstartterminDatum" class="form-input-date"
                                        @if(isset($kursstartterminDatum))
                                            value="{{ $kursstartterminDatum }}"
                                        @else
                                            value="{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d') }}"
                                        @endif
                                       x-model="kursstatterminDatum" @change="kursendterminDatum = kursstatterminDatum"
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
                            <label for="kursendtermin" class="form-label">letztmögliches Ende:</label>
                            <div class="form-field flex">
                                <input type="date" name="kursendterminDatum" id="kursendterminDatum" class="form-input-date @if(isset($danger)) is-invalid @endif"
                                        @if(isset($kursendterminDatum))
                                            value="{{ $kursendterminDatum }}"
                                        @else
                                            value="{{ Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('Y-m-d') }}"
                                        @endif
                                        x-model="kursendterminDatum"
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
                            <label for="kurslaenge" class="form-label">Dauer:</label>
                            <input type="time" name="kurslaenge" id="kurslaenge"  class="form-input-date @if(isset($danger)) is-invalid @endif"
                                   @if(isset($kurslaenge))
                                       value="{{ $kurslaenge }}"
                                   @else
                                       value="{{ $coursedate->kurslaenge }}"
                                @endif
                            >
                        </div>

                        <div class="form-field">
                            <label for="trainer_id" class="form-label">{{ $organiser->trainerUeberschrift }}:</label>
                            <div class="form-input-text">
                                @foreach($coursedate->users as $user)
                                    {{ $user->vorname }} {{ $user->nachname }}<br>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">Name:</label>
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
                            <label for="sportgeraetanzahl" class="form-label">Anzahl der möglichen Teilnehmer der {{ $organiser->materialUeberschrift }}:</label>
                            <select name="sportgeraetanzahl">
                                <option value="0"  @selected(old('sportgeraetanzahl') ?? 0 == $coursedate->sportgeraetanzahl)>
                                    maximale Teilnehmer
                                </option>
                                @for($i = 1; $i <= $sportgeraetanzahlMax; $i++)
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

                        <div class="form-field">
                                <label class="form-label">Information zum Termin:</label>
                                <textarea name="kursInformation" class="form-input-textarea @if($errors->has('kursInformation')) is-invalid @endif">{{ old('kursInformation', $coursedate->kursInformation)}}</textarea>
                                @if ($errors->has('kursInformation'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('kursInformation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                </div>
                <div class="form-footer">
                    <a href="{{ route('backend.courseDate.index') }}" class="form-button">
                        {{ __('main.back') }}
                    </a>
                    <button type="submit" class="form-button">
                        {{ __('main.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
