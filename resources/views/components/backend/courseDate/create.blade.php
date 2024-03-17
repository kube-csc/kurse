<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Course Date Create') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!! __('backend.Course Date Create Help') !!}
                </p>
            </div>
        </div>
    </x-slot>
    <div class="main-box">s
            <div class="box">
                <form action="{{ route('backend.courseDate.store') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <div class="form-card" x-data="{ kursstatterminDatum: '{{ $kursstartterminDatum }}', kursendterminDatum: '{{ $kursstartterminDatum }}' }">
                            <div class="form-field">
                                <label for="kursstarttermin" class="form-label">Start Datum</label>
                                <div class="form-field flex">
                                    <input type="date" name="kursstartterminDatum" id="kursstartterminDatum" class="form-input-date" value="{{ $kursstartterminDatum }}" x-model="kursstatterminDatum" @change="kursendterminDatum = kursstatterminDatum">
                                    <input type="time" name="kursstartterminTime" id="kursstartterminTime" class="form-input-date" value="{{ $kursstartterminTime }}">
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="kursendtermin" class="form-label">End Datum</label>
                                <div class="form-field flex">
                                    <input type="date" name="kursendterminDatum" id="kursendterminDatum" class="form-input-date @if(isset($danger)) is-invalid @endif" value="{{ $kursendterminDatum }}" x-model="kursendterminDatum">
                                    <input type="time" name="kursendterminTime" id="kursendterminTime" class="form-input-date @if(isset($danger)) is-invalid @endif" value="{{ $kursendterminTime }}">
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="kurslaenge" class="form-label">Kursdauer</label>
                                <input type="time" name="kurslaenge" id="kurslaenge"  class="form-input-date @if(isset($danger)) is-invalid @endif" value="{{ $kurslaenge }}">
                            </div>

                            <div class="form-field">
                                <label for="trainer_id" class="form-label">{{ $organiser->trainerUeberschrift }}:</label>
                                <div class="form-input-text">{{ Auth::user()->vorname }} {{ Auth::user()->nachname }}</div>
                            </div>

                            <div class="form-field">
                                <label for="course_id" class="form-label">Kursname</label>
                                <select name="course_id">
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}"  @selected(old('couse_id') ?? $course->id  == $course_id)>
                                            {{ $course->kursName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="sportgeraetanzahl" class="form-label">Anzahl Sportgeräte</label>
                                <select name="sportgeraetanzahl">
                                    <option value="0"  @selected(old('sportgeraetanzahl') ?? 0 == $sportgeraetanzahl)>
                                        alle Sportgeräte
                                    </option>
                                    @for($i = 1; $i <= $sportgeraetanzahlMax; $i++)
                                        <option value="{{ $i }}"  @selected(old('sportgeraetanzahl') ?? $i == $sportgeraetanzahl)>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-field">
                                <label class="form-label">Information zum Kurs:</label>
                                <textarea name="kursInformation" class="form-input-textarea @if($errors->has('kursInformation')) is-invalid @endif">{{ old('kursInformation', '')}}</textarea>
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
                            Zurück
                        </a>
                        <button type="submit" class="form-button ">
                            Eintragen
                        </button>
                    </div>
                </form>
            </div>
        </div>
</x-app-layout>
