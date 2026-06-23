<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            Schlechtwetter-Absage / Terminverschiebung
        </h2>
    </x-slot>

    <div class="main-box">
        <div class="box">
            <form action="{{ route('backend.courseDate.badWeather.update', $coursedate->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <div
                        class="form-card"
                        x-data="{ actionType: '{{ old('action_type', 'postpone') }}' }"
                    >
                        <div class="form-field">
                            <label class="form-label">Kurs:</label>
                            <div class="form-input-text">{{ $coursedate->getCousename->kursName }}</div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Aktueller Termin:</label>
                            <div class="form-input-text">
                                {{ \Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('d.m.Y H:i') }} Uhr
                                bis
                                {{ \Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('d.m.Y H:i') }} Uhr
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="action_type" class="form-label">Aktion:</label>
                            <select name="action_type" id="action_type" x-model="actionType">
                                <option value="postpone" @selected(old('action_type', 'postpone') === 'postpone')>Termin verschieben</option>
                                <option value="cancel" @selected(old('action_type') === 'cancel')>Termin absagen</option>
                            </select>
                            @error('action_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-field" x-show="actionType === 'postpone'" x-cloak>
                            <label for="new_start_date" class="form-label">Neuer Starttermin:</label>
                            <div class="form-field flex">
                                <input
                                    type="date"
                                    name="new_start_date"
                                    id="new_start_date"
                                    class="form-input-date @error('new_start_date') is-invalid @enderror"
                                    value="{{ old('new_start_date', \Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d')) }}"
                                >
                                <input
                                    type="time"
                                    name="new_start_time"
                                    id="new_start_time"
                                    class="form-input-date @error('new_start_time') is-invalid @enderror"
                                    value="{{ old('new_start_time', \Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('H:i')) }}"
                                >
                            </div>
                            @error('new_start_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @error('new_start_time')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="kurs_information" class="form-label">Kursinformation:</label>
                            <textarea
                                name="kurs_information"
                                id="kurs_information"
                                class="form-input-textarea @error('kurs_information') is-invalid @enderror"
                            >{{ old('kurs_information', $coursedate->kursInformation) }}</textarea>
                            @error('kurs_information')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="trainer_message" class="form-label">Text vom Trainer an Teilnehmer (Vorschlag):</label>
                            <textarea
                                name="trainer_message"
                                id="trainer_message"
                                class="form-input-textarea @error('trainer_message') is-invalid @enderror"
                            >{{ old('trainer_message', $trainerMessageSuggestion) }}</textarea>
                            @error('trainer_message')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('backend.courseDate.index') }}" class="form-button">
                        {{ __('main.back') }}
                    </a>
                    <button type="submit" class="form-button">
                        Speichern und Teilnehmer informieren
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
