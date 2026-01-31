<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
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
                </button>
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
                <label for="course_id" class="form-label">{{ $pickedSportSections->count() }} zugewiesene Abteilung(en) für Sportgeräte:</label>
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
                <label for="course_id" class="form-label">{{ $sportSections->count() }} nicht zugewiesene Abteilung(en):</label>
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

            <form action="{{ route('backend.organiser.update', $organiser->id) }}" method="POST" enctype="multipart/form-data">
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

                            <label class="form-label">Bezeichnung des Kurs / Fahrt / Training:</label>
                            <input type="text" name="kurseUeberschrift" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old( 'kurseUeberschrift', $organiser->kurseUeberschrift) }}">
                            @error('kurseUeberschrift')
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
                            <label class="form-label">Headerbild (Upload):</label>
                            @php
                                $grossFilename = !empty($organiser->veranstaltungHeader) ? ltrim($organiser->veranstaltungHeader, '/') : null;
                                $grossUrl = $grossFilename ? asset('storage/organisers/' . $grossFilename) : null;
                            @endphp

                            @if($grossUrl)
                                <div class="mb-2">
                                    <img src="{{ $grossUrl }}" alt="Headerbild" style="max-width: 100%; max-height: 200px; object-fit: contain;" />
                                </div>
                            @endif

                            <div class="mb-2" style="display:flex; align-items:center; gap:8px;">
                                <input type="file" name="veranstaltungHeader" accept="image/*" class="form-input-text @if($errors->has('veranstaltungHeader')) is-invalid @endif">

                                @if($grossUrl)
                                    <button
                                        type="button"
                                        class="image-delete-icon-btn"
                                        data-delete-url="{{ route('backend.organiser.destroyVeranstaltungHeader', $organiser) }}"
                                        data-confirm="Headerbild wirklich löschen?"
                                        title="Headerbild löschen"
                                    >
                                        <box-icon name='trash'></box-icon>
                                    </button>
                                @endif
                            </div>

                            @if ($errors->has('veranstaltungHeader'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('veranstaltungHeader') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Headerbild klein (Handy) (Upload):</label>
                            @php
                                $kleinFilename = !empty($organiser->veranstaltungHeaderKlein) ? ltrim($organiser->veranstaltungHeaderKlein, '/') : null;
                                $kleinUrl = $kleinFilename ? asset('storage/organisers/' . $kleinFilename) : null;
                            @endphp

                            @if($kleinUrl)
                                <div class="mb-2">
                                    <img src="{{ $kleinUrl }}" alt="Headerbild klein" style="max-width: 100%; max-height: 200px; object-fit: contain;" />
                                </div>
                            @endif

                            <div class="mb-2" style="display:flex; align-items:center; gap:8px;">
                                <input type="file" name="veranstaltungHeaderKlein" accept="image/*" class="form-input-text @if($errors->has('veranstaltungHeaderKlein')) is-invalid @endif">

                                @if($kleinUrl)
                                    <button
                                        type="button"
                                        class="image-delete-icon-btn"
                                        data-delete-url="{{ route('backend.organiser.destroyVeranstaltungHeaderKlein', $organiser) }}"
                                        data-confirm="Kleines Headerbild wirklich löschen?"
                                        title="Kleines Headerbild löschen"
                                    >
                                        <box-icon name='trash'></box-icon>
                                    </button>
                                @endif
                            </div>

                            @if ($errors->has('veranstaltungHeaderKlein'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('veranstaltungHeaderKlein') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Lange Beschreibung der Veranstaltung:</label>
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
                            <label class="form-label">Überschrift Beschreibung:</label>
                            <input type="text" name="sportartUeberschrift" class="form-input-text @if(isset($danger)) is-invalid @endif" x-model="sportartUeberschrift">
                            @error('sportartUeberschrift')
                            <div class="invalid-feedback" role="alert">
                                {{-- Test: Wie wird der Validate ausgeben --}}
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                            <label class="form-label">Langer Text <span x-text="sportartUeberschrift"></span> Beschreibung:</label>
                            <textarea name="sportartBeschreibungLang" class="form-input-textarea @if($errors->has('sportartBeschreibungLang')) is-invalid @endif">{{ old('sportartBeschreibungLang', $organiser->getOrganiserInformation->sportartBeschreibungLang) }}</textarea>
                            @if ($errors->has('sportartBeschreibungLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('sportartBeschreibungLang') }}</strong>
                                </span>
                            @endif

                            <label class="form-label">Kurzer Text <span x-text="sportartUeberschrift"></span> Beschreibung:</label>
                            <textarea name="sportartBeschreibungKurz" class="form-input-textarea @if($errors->has('sportartBeschreibungKurz')) is-invalid @endif">{{ old('sportartBeschreibungKurz', $organiser->getOrganiserInformation->sportartBeschreibungKurz) }}</textarea>
                            @if ($errors->has('sportartBeschreibungKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('sportartBeschreibungKurz') }}</strong>
                                </span>
                            @endif
                        </div>


                        <div class="form-field" x-data="{ materialUeberschrift: '{{ old('materialUeberschrift', $organiser->materialUeberschrift) }}' }">
                            <label class="form-label">Überschrift Materialbeschreibung:</label>
                            <input type="text" name="materialUeberschrift" class="form-input-text @if(isset($danger)) is-invalid @endif" x-model="materialUeberschrift">
                            @error('materialUeberschrift')
                            <div class="invalid-feedback" role="alert">
                                {{-- Test: Wie wird der Validate ausgeben --}}
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                            <label class="form-label">Lange <span x-text="materialUeberschrift"></span> Beschreibung:</label>
                            <textarea name="materialBeschreibungLang" class="form-input-textarea @if($errors->has('materialBeschreibungLang')) is-invalid @endif">{{ old('materialBeschreibungLang', $organiser->getOrganiserInformation->materialBeschreibungLang) }}</textarea>
                            @if ($errors->has('materialBeschreibungLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('materialBeschreibungLang') }}</strong>
                                </span>
                            @endif

                            <label class="form-label">Kurze <span x-text="materialUeberschrift"></span> Beschreibung:</label>
                            <textarea name="materialBeschreibungKurz" class="form-input-textarea @if($errors->has('materialBeschreibungKurz')) is-invalid @endif">{{ old('materialBeschreibungKurz', $organiser->getOrganiserInformation->materialBeschreibungKurz) }}</textarea>
                            @if ($errors->has('materialBeschreibungKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('materialBeschreibungKurz') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Langer Text für Mitglied werden:</label>
                            <textarea name="mitgliedschaftLang" class="form-input-textarea @if($errors->has('mitgliedschaftLang')) is-invalid @endif">{{ old('mitgliedschaftLang', $organiser->getOrganiserInformation->mitgliedschaftLang) }}</textarea>
                            @if ($errors->has('mitgliedschaftLang'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('mitgliedschaftLang') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Kurzer Text für Mitglied werden:</label>
                            <textarea name="mitgliedschaftKurz" class="form-input-textarea @if($errors->has('mitgliedschaftKurz')) is-invalid @endif">{{ old('mitgliedschaftKurz', $organiser->getOrganiserInformation->mitgliedschaftKurz) }}</textarea>
                            @if ($errors->has('mitgliedschaftKurz'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('mitgliedschaftKurz') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Text wenn keine Kurse / Fahrten / Trainings eingestellt ist:</label>
                            <textarea name="keineKurse" class="form-input-textarea @if($errors->has('keineKurse')) is-invalid @endif">{{ old('keineKurse', $organiser->getOrganiserInformation->keineKurse) }}</textarea>
                            @if ($errors->has('keineKurse'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('keineKurse') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Information für Kurse / Fahrten / Trainings:</label>
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
                        {{ __('main.back') }}
                    </a>
                    <button type="submit" class="form-button">
                        {{ __('main.save') }}
                    </button>
                </div>
            </form>

            <script>
                // DELETE ohne verschachtelte <form>-Tags: sendet korrekten DELETE an die Bild-Routen
                document.addEventListener('click', async (e) => {
                    const btn = e.target.closest('.image-delete-btn, .image-delete-icon-btn');
                    if (!btn) return;

                    const url = btn.getAttribute('data-delete-url');
                    const confirmText = btn.getAttribute('data-confirm') || 'Wirklich löschen?';
                    if (!url) return;

                    if (!window.confirm(confirmText)) return;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token || '',
                                'Accept': 'text/html,application/xhtml+xml',
                                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                            },
                            body: new URLSearchParams({ _method: 'DELETE' })
                        });

                        if (res.ok) {
                            window.location.reload();
                            return;
                        }

                        alert('Löschen fehlgeschlagen (' + res.status + ').');
                    } catch (err) {
                        console.error(err);
                        alert('Löschen fehlgeschlagen.');
                    }
                });
            </script>
        </div>
    </div>
</x-app-layout>
