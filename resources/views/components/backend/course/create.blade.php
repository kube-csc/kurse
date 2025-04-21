<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            {{ __('backend.Course Create') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.Course Create Help') !!}
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

            <form action="{{ route('backend.course.store') }}" method="POST">
                @csrf
                @method('POST')
                <div class="form-group">
                    <div class="form-card">
                        <div class="form-field">
                            <label class="form-label">Name:</label>
                            <input type="text" name="kursName" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old('kursName') }}">
                            @error('kursName')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                            <br><br>
                            <label class="form-label">Trainer ist erforderlich:</label>
                            <span class="form-label">
                            <input type="checkbox" name="trainer" value="1" @if(old('trainer')==1) checked @endif>
                            erforderlich
                            </span>
                            <br>
                            <label class="form-label">Schnupperkurs:</label>
                            <span class="form-label">
                                <input type="checkbox" name="schnupperkurs" value="1" @if(old('schnupperkurs')==1) checked @endif>
                                Ja
                            </span>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Beschreibung:</label>
                            <textarea name="kursBeschreibung" class="form-input-textarea @if($errors->has('kursBeschreibung')) is-invalid @endif">{{ old('kursBeschreibung') }}</textarea>
                            @if ($errors->has('kursBeschreibung'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('kursBeschreibung') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('backend.course.index') }}" class="form-button">
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
