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
                        <a href="{{ route('backend.course.destroySportSection',
                                        [
                                            'courseId'                => $course->id,
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
                        <a href="{{ route('backend.course.pickSportSection',
                                    [
                                        'courseId'              => $course->id,
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

            <form action="{{ route('backend.course.update', $course->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card">
                        <div class="form-field">
                            <label class="form-label">Kursname:</label>
                            <input type="text" name="kursName" class="form-input-text @if(isset($danger)) is-invalid @endif" value="{{ old('kursName', $course->kursName) }}">
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
