<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            {{ __('backend.Sport Equipment Create') }}
        </h2>
    </x-slot>

    <div class="main-box">
        <div class="box">
            <form action="{{ route('backend.sportEquipment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <div class="form-card">

                        <div class="form-field">
                            <label class="form-label">Abteilung / Sportart (Pflicht):</label>
                            <select name="sportSection_id" class="form-input-text @error('sportSection_id') is-invalid @enderror" required>
                                <option value="">Bitte wählen…</option>
                                @foreach(($sportSections ?? []) as $sportSection)
                                    <option value="{{ $sportSection->id }}" @selected(old('sportSection_id') == $sportSection->id)>
                                        {{ $sportSection->abteilung }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sportSection_id')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Sportgerät:</label>
                            <input type="text" name="sportgeraet" class="form-input-text @error('sportgeraet') is-invalid @enderror" value="{{ old('sportgeraet') }}">
                            @error('sportgeraet')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Datum der Anschaffung:</label>
                            <input type="date" name="anschafdatum" id="anschafdatum" class="form-input-date @error('anschafdatum') is-invalid @enderror" value="{{ old('anschafdatum') }}">
                            @error('anschafdatum')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Datum der Verschrottung:</label>
                            <input type="date" name="verschrottdatum" id="verschrottdatum" class="form-input-date @error('verschrottdatum') is-invalid @enderror" value="{{ old('verschrottdatum') }}">
                            @error('verschrottdatum')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Bild (optional):</label>
                            <input type="file" name="bild" accept="image/*" class="form-input-text @error('bild') is-invalid @enderror">
                            @error('bild')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Anzahl der möglichen Sportler:</label>
                            <input type="number" min="1" name="sportleranzahl" class="form-input-text @error('sportleranzahl') is-invalid @enderror" value="{{ old('sportleranzahl', 1) }}">
                            @error('sportleranzahl')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Länge in Meter:</label>
                            <input type="text" name="laenge" class="form-input-text @error('laenge') is-invalid @enderror" value="{{ old('laenge', 0) }}">
                            @error('laenge')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Breite in Meter:</label>
                            <input type="text" name="breite" class="form-input-text @error('breite') is-invalid @enderror" value="{{ old('breite', 0) }}">
                            @error('breite')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Höhe in Meter:</label>
                            <input type="text" name="hoehe" class="form-input-text @error('hoehe') is-invalid @enderror" value="{{ old('hoehe', 0) }}">
                            @error('hoehe')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Gewicht in kg:</label>
                            <input type="text" name="gewicht" class="form-input-text @error('gewicht') is-invalid @enderror" value="{{ old('gewicht', 0) }}">
                            @error('gewicht')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Tragkraft in kg:</label>
                            <input type="text" name="tragkraft" class="form-input-text @error('tragkraft') is-invalid @enderror" value="{{ old('tragkraft', 0) }}">
                            @error('tragkraft')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Typ:</label>
                            <textarea name="typ" class="form-input-textarea @error('typ') is-invalid @enderror">{{ old('typ') }}</textarea>
                            @error('typ')
                            <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('backend.sportEquipment.index') }}" class="form-button">
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

