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
                            <label class="form-label">Veranstalter Beschreibung:</label>
                            <textarea name="veranstalterBeschreibung" class="form-input-textarea @if($errors->has('veranstalterBeschreibung')) is-invalid @endif">{{ old('veranstalterBeschreibung', $organiser->veranstalterBeschreibung) }}</textarea>
                            @if ($errors->has('veranstalterBeschreibung'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('veranstalterBeschreibung') }}</strong>
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
