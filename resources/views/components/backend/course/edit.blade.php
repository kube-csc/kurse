<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Course Edit') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.Course Edit Help') !!}
                </p>
            </div>
       </div>
    </x-slot>
    <div class="main-box">
        <div class="box">
            <form action="{{ route('backend.course.update', $course->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card">
                        <div class="form-field">
                            <label class="form-label">Kursname:</label>
                            <input type="text" name="kursName" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ $course->kursName }}">
                            @error('kursName')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Kursbeschreibung:</label>
                            <textarea name="kursBeschreibung" class="form-input-textarea @if($errors->has('kursBeschreibung')) is-invalid @endif">{{ old('kursBeschreibung', $course->kursBeschreibung) }}</textarea>
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
