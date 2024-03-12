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
                            <label class="form-label">Veranstaltung:</label>
                            <input type="text" name="veranstaltung" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old( 'veranstaltung', $organiser->veranstaltung) }}">
                            @error('veranstaltung')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror

                            <label class="form-label">Trainer Bezeichnung:</label>
                            <input type="text" name="trainerUeberschrift" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old( 'trainerUeberschrift', $organiser->trainerUeberschrift) }}">
                            @error('trainerUeberschrift')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Aktuelle Domain:</label>
                            <p class="text">{{ $_SERVER['HTTP_HOST'] }}</p>
                            <label class="form-label">Domain der Veranstaltung:</label>
                            <input type="text" name="veranstaltungDomain" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old( 'veranstaltungDomain', $organiser->veranstaltungDomain) }}">
                            @error('veranstaltungDomain')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Veranstaltung Beschreibung:</label>
                            <textarea name="veranstaltungBeschreibungLang" class="form-input-textarea @if($errors->has('veranstaltungBeschreibungLang')) is-invalid @endif">{{ old('veranstaltungBeschreibungLang', $organiser->getOrganiserInformation->veranstaltungBeschreibungLang) }}</textarea>
                            @if ($errors->has('veranstaltungBeschreibungLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('veranstaltungBeschreibungLang') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Kurze Beschreibung der Veranstaltung:</label>
                            <textarea name="veranstaltungBeschreibungKurz" class="form-input-textarea @if($errors->has('veranstaltungBeschreibungKurz')) is-invalid @endif">{{ old('veranstaltungBeschreibungKurz', $organiser->getOrganiserInformation->veranstaltungBeschreibungKurz) }}</textarea>
                            @if ($errors->has('veranstaltungBeschreibungKurz'))
                                <span class="invalid-feedback" role="alert">
                                 {{-- Test: Wie wird der Validate ausgeben --}}
                                    <strong>{{ $errors->first('veranstaltungBeschreibungKurz') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field" x-data="{ sportartUeberschrift: '{{ old('sportartUeberschrift', $organiser->sportartUeberschrift) }}' }">
                            <label class="form-label">Überschrift Beschreibungstext:</label>
                            <input type="text" name="sportartUeberschrift" class="form-input-text @if(isset($danger)) is-invalid @endif" x-model="sportartUeberschrift">
                            @error('sportartUeberschrift')
                            <div class="invalid-feedback" role="alert">
                                {{-- Test: Wie wird der Validate ausgeben --}}
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                            <label class="form-label"><span x-text="sportartUeberschrift"></span> Beschreibung langer Text:</label>
                            <textarea name="sportartBeschreibungLang" class="form-input-textarea @if($errors->has('sportartBeschreibungLang')) is-invalid @endif">{{ old('sportartBeschreibungLang', $organiser->getOrganiserInformation->sportartBeschreibungLang) }}</textarea>
                            @if ($errors->has('sportartBeschreibungLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('sportartBeschreibungLang') }}</strong>
                                </span>
                            @endif

                            <label class="form-label"><span x-text="sportartUeberschrift"></span> Beschreibung kurz Text:</label>
                            <textarea name="sportartBeschreibungKurz" class="form-input-textarea @if($errors->has('sportartBeschreibungKurz')) is-invalid @endif">{{ old('sportartBeschreibungKurz', $organiser->getOrganiserInformation->sportartBeschreibungKurz) }}</textarea>
                            @if ($errors->has('sportartBeschreibungKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('sportartBeschreibungKurz') }}</strong>
                                </span>
                            @endif
                        </div>


                        <div class="form-field" x-data="{ materialUeberschrift: '{{ old('materialUeberschrift', $organiser->materialUeberschrift) }}' }">
                            <label class="form-label">Überschrift Materialbeschreibung :</label>
                            <input type="text" name="materialUeberschrift" class="form-input-text @if(isset($danger)) is-invalid @endif" x-model="materialUeberschrift">
                            @error('materialUeberschrift')
                            <div class="invalid-feedback" role="alert">
                                {{-- Test: Wie wird der Validate ausgeben --}}
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                            <label class="form-label"><span x-text="materialUeberschrift"></span> Lange Materialbeschreibung für die Veranstaltung:</label>
                            <textarea name="materialBeschreibungLang" class="form-input-textarea @if($errors->has('materialBeschreibungLang')) is-invalid @endif">{{ old('materialBeschreibungLang', $organiser->getOrganiserInformation->materialBeschreibungLang) }}</textarea>
                            @if ($errors->has('materialBeschreibungLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('materialBeschreibungLang') }}</strong>
                                </span>
                            @endif

                            <label class="form-label"><span x-text="materialUeberschrift"></span> Kurze Materialbeschreibung für die Veranstaltung:</label>
                            <textarea name="materialBeschreibungKurz" class="form-input-textarea @if($errors->has('materialBeschreibungKurz')) is-invalid @endif">{{ old('materialBeschreibungKurz', $organiser->getOrganiserInformation->materialBeschreibungKurz) }}</textarea>
                            @if ($errors->has('materialBeschreibungKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('materialBeschreibungKurz') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Text lang Mitglied werden:</label>
                            <textarea name="mitgliedschaftLang" class="form-input-textarea @if($errors->has('mitgliedschaftLang')) is-invalid @endif">{{ old('mitgliedschaftLang', $organiser->getOrganiserInformation->mitgliedschaftLang) }}</textarea>
                            @if ($errors->has('mitgliedschaftLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('mitgliedschaftLang') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Text kurz Mitglied werden:</label>
                            <textarea name="mitgliedschaftKurz" class="form-input-textarea @if($errors->has('mitgliedschaftKurz')) is-invalid @endif">{{ old('mitgliedschaftKurz', $organiser->getOrganiserInformation->mitgliedschaftKurz) }}</textarea>
                            @if ($errors->has('mitgliedschaftKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('mitgliedschaftKurz') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Text keine Kurse eingestellt:</label>
                            <textarea name="keineKurse" class="form-input-textarea @if($errors->has('keineKurse')) is-invalid @endif">{{ old('keineKurse', $organiser->getOrganiserInformation->keineKurse) }}</textarea>
                            @if ($errors->has('keineKurse'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('keineKurse') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Information für Termine:</label>
                            <textarea name="terminInformation" class="form-input-textarea @if($errors->has('terminInformation')) is-invalid @endif">{{ old('terminInformation', $organiser->getOrganiserInformation->terminInformation) }}</textarea>
                            @if ($errors->has('terminInformation'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('terminInformation') }}</strong>
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
