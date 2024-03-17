<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Sport Equipment Edit') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.Sport Equipment Edit Help') !!}
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
            <form action="{{ route('backend.sportEquipment.update', $sportEquipment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card">
                        <div class="form-field">
                            <label class="form-label">Sportgerät:</label>
                            <input type="text" name="sportgeraet" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old('sportgeraet', $sportEquipment->sportgeraet) }}">
                            @error('sportgeraet')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="kursendtermin" class="form-label">Datum der Anschaffung:</label>
                            <div class="form-field flex">
                                <input type="date" name="anschafdatum" id="anschafdatum" class="form-input-date @if(isset($danger)) is-invalid @endif"
                                       @if(isset($anschafdatum))
                                           value="{{ $anschafdatum }}"
                                       @else
                                           value="{{ Illuminate\Support\Carbon::parse($sportEquipment->anschafdatum)->format('Y-m-d') }}"
                                      @endif

                                >
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="kursendtermin" class="form-label">Datum der Stillegung:</label>
                            <div class="form-field flex">
                                <input type="date" name="verschrottdatum" id="verschrottdatum" class="form-input-date @if(isset($danger)) is-invalid @endif"
                                       @if(isset($verschrottdatum))
                                           @if($verschrottdatum != null)
                                               value="{{ $verschrottdatum }}"
                                           @endif
                                       @else
                                           @if($sportEquipment->verschrottdatum != null)
                                               value="{{ Illuminate\Support\Carbon::parse($sportEquipment->verschrottdatum)->format('Y-m-d') }}"
                                           @endif
                                      @endif
                                >
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Länge in Meter: </label>
                            <input type="text" name="laenge" class="form-input-text @if($errors->has('laenge')) is-invalid @endif" value="{{ old('laenge', $sportEquipment->laenge) }}">
                            @if ($errors->has('laenge'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('laenge') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Breite in Meter: </label>
                            <input type="text" name="breite" class="form-input-text @if($errors->has('breite')) is-invalid @endif" value="{{ old('breite', $sportEquipment->breite) }}">
                            @if ($errors->has('breite'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('breite') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Höhe in Meter: </label>
                            <input type="text" name="hoehe" class="form-input-text @if($errors->has('hoehe')) is-invalid @endif" value="{{ old('hoehe', $sportEquipment->hoehe) }}">
                            @if ($errors->has('hoehe'))
                                <span class="invalid-feedback" role="alert">
                                   <strong>{{ $errors->first('hoehe') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Gewicht in kg:</label>
                            <input type="text" name="gewicht" class="form-input-text @if($errors->has('gewicht')) is-invalid @endif" value="{{ old('gewicht', $sportEquipment->gewicht) }}">
                            @if ($errors->has('gewicht'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('gewicht') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Tragkraft in kg:</label>
                            <input type="text" name="tragkraft" class="form-input-text @if($errors->has('tragkraft')) is-invalid @endif" value="{{ old('tragkraft', $sportEquipment->tragkraft) }}">
                            @if ($errors->has('tragkraft'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tragkraft') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Sportleranzahl:</label>
                            <input type="text" name="sportleranzahl" class="form-input-text @if($errors->has('sportleranzahl')) is-invalid @endif" value="{{ old('sportleranzahl', $sportEquipment->sportleranzahl) }}">
                            @if ($errors->has('sportleranzahl'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('sportleranzahl') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-field">
                            <label class="form-label">Typ:</label>
                            <textarea name="typ" class="form-input-textarea @if($errors->has('typ')) is-invalid @endif">{{ old('typ', $sportEquipment->typ) }}</textarea>
                            @if ($errors->has('typ'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('typ') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('backend.sportEquipment.index') }}" class="form-button">
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
