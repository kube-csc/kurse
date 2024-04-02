<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            {{ __('backend.Course Dates') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.Course Date Edit Help') !!}
                </p>
            </div>
       </div>
    </x-slot>
    <div class="main-box">
        <div class="box">

            <form action="{{ route('backend.courseDate.updateBooked', $coursedate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card" x-data="{ kursstatterminDatum: '{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d') }}', kursendterminDatum: '{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d') }}' }">
                          <div class="form-field">
                            <label for="sportgeraetanzahl" class="form-label">Anzahl der möglichen Teilnehmer der {{ $organiser->materialUeberschrift }}:</label>
                            <select name="sportgeraetanzahl">
                                <option value="0"  @selected(old('sportgeraetanzahl') ?? 0 == $coursedate->sportgeraetanzahl)>
                                    maximale Teilnehmer
                                </option>
                                @for($i = 1; $i <= $sportgeraetanzahlMax; $i++)
                                    <option value="{{ $i }}"
                                            @if(isset($kursendtermin))
                                                @selected(old('sportgeraetanzahl') ?? $i == $sportgeraetanzahl)
                                            @else
                                                @selected(old('sportgeraetanzahl') ?? $i == $coursedate->sportgeraetanzahl)
                                           @endif
                                    >
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-field">
                                <label class="form-label">Information zum Termin:</label>
                                <textarea name="kursInformation" class="form-input-textarea @if($errors->has('kursInformation')) is-invalid @endif">{{ old('kursInformation', $coursedate->kursInformation)}}</textarea>
                                @if ($errors->has('kursInformation'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('kursInformation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                </div>
                <div class="form-footer">
                    <a href="{{ route('backend.courseDate.index') }}" class="form-button">
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
