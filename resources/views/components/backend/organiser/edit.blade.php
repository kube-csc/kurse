<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Organiser Edit') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.Organiser Edit Help') !!}
                </p>
            </div>
            <div x-data="{ openHelpEdit: false }" class="text-left">
                <button @click="openHelpEdit = !openHelpEdit">
                    {{ __('backend.Edit help HTML button') }}
                    Hilfe zum editieren</button>
                <div class="help-box" x-show="openHelpEdit" @click.away="openHelpEdit = false">
                    <p class="help-text">
                        {!! __('backend.Edit help HTML') !!}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>
    <div class="main-box">
        <div class="box">
            <div class="form-field">
                <label for="course_id" class="form-label">{{ $pickedSportSections->count() }} zugewiesende Abteilung(en) für Sportgeräte</label>
                <div class="form-box">
                    @foreach($pickedSportSections as $pickedSportSection)
                        <a href="{{ route('backend.organiser.destroySportSection',
                                        [
                                            'organiserId'             => $organiser->id,
                                            'destroySportSectionId'   => $pickedSportSection->sport_section_id,
                                       ]
                                        ) }}"
                        >
                            <button class="form-button"><box-icon name='minus-circle'></box-icon>
                                {{ $pickedSportSection->abteilung }}
                            </button>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="form-field">
                <label for="course_id" class="form-label">{{ $sportSections->count() }} nicht zugewiesende Abteilung(en)</label>
                <div class="form-box">
                    @foreach($sportSections as $sportSection)
                        <a href="{{ route('backend.organiser.pickSportSection',
                                    [
                                        'organiserId'           => $organiser->id,
                                        'pickSportSectionId'    => $sportSection->id
                                    ] ) }}"
                        >
                            <button class="form-button">
                                <box-icon name='plus-circle'></box-icon>
                                {{ $sportSection->abteilung }}
                            </button>
                        </a>
                    @endforeach
                </div>
            </div>

            <form action="{{ route('backend.organiser.update', $organiser->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card">
                        <div class="form-field">
                            <label class="form-label">Veranstalter:</label>
                            <input type="text" name="veranstalter" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old( 'veranstalter', $organiser->veranstalter) }}">
                            @error('veranstalter')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Aktuelle Domain:</label>
                            <p class="text">{{ $_SERVER['HTTP_HOST'] }}</p>
                            <label class="form-label">Veranstalter Domain:</label>
                            <input type="text" name="veranstalterDomain" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old( 'veranstalterDomain', $organiser->veranstalterDomain) }}">
                            @error('veranstalterDomain')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Veranstalter Beschreibung:</label>
                            <textarea name="veranstalterBeschreibungLang" class="form-input-textarea @if($errors->has('veranstalterBeschreibungLang')) is-invalid @endif">{{ old('veranstalterBeschreibungLang', $organiser->veranstalterBeschreibungLang) }}</textarea>
                            @if ($errors->has('veranstalterBeschreibungLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('veranstalterBeschreibungLang') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Veranstalterbeschreibung kurz:</label>
                            <textarea name="veranstalterBeschreibungKurz" class="form-input-textarea @if($errors->has('veranstalterBeschreibungKurz')) is-invalid @endif">{{ old('veranstalterBeschreibungKurz', $organiser->veranstalterBeschreibungKurz) }}</textarea>
                            @if ($errors->has('veranstalterBeschreibungKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('veranstalterBeschreibungKurz') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Sportartbeschreibung langer Text:</label>
                            <textarea name="sportartBeschreibungLang" class="form-input-textarea @if($errors->has('sportartBeschreibungLang')) is-invalid @endif">{{ old('sportartBeschreibungLang', $organiser->sportartBeschreibungLang) }}</textarea>
                            @if ($errors->has('sportartBeschreibungLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('sportartBeschreibungLang') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Sportartbeschreibung kurzer Text:</label>
                            <textarea name="sportartBeschreibungKurz" class="form-input-textarea @if($errors->has('sportartBeschreibungKurz')) is-invalid @endif">{{ old('sportartBeschreibungKurz', $organiser->sportartBeschreibungKurz) }}</textarea>
                            @if ($errors->has('sportartBeschreibungKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('sportartBeschreibungKurz') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Materialbeschreibung lang für den Kurs:</label>
                            <textarea name="materialBeschreibungLang" class="form-input-textarea @if($errors->has('materialBeschreibungLang')) is-invalid @endif">{{ old('materialBeschreibungLang', $organiser->materialBeschreibungLang) }}</textarea>
                            @if ($errors->has('materialBeschreibungLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('materialBeschreibungLang') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Materialbeschreibung kurz für den Kurs:</label>
                            <textarea name="materialBeschreibungKurz" class="form-input-textarea @if($errors->has('materialBeschreibungKurz')) is-invalid @endif">{{ old('materialBeschreibungKurz', $organiser->materialBeschreibungKurz) }}</textarea>
                            @if ($errors->has('materialBeschreibungKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('materialBeschreibungKurz') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Text Keine Kurse eingestellt:</label>
                            <textarea name="keineKurse" class="form-input-textarea @if($errors->has('keineKurse')) is-invalid @endif">{{ old('keineKurse', $organiser->keineKurse) }}</textarea>
                            @if ($errors->has('keineKurse'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('keineKurse') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('backend.organiser.index') }}" class="form-button">
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
