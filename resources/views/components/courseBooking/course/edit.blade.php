<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Course Dates') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.Course Book Participant Help') !!}
                </p>
            </div>
        </div>
    </x-slot>
    <div class="main-box">
        <div class="box">

            <form action="{{ route('courseBooking.course.update', $coursedate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card" x-data="{ kursstatterminDatum: '{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d') }}', kursendterminDatum: '{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d') }}' }">
                        <div class="form-field">
                            <label for="kursstarttermin" class="form-label">Start Datum:</label>
                            <div class="form-field flex">
                                <div class="form-input-text">
                                     {{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('d.m.Y') }}
                                </div>
                                @if($courseBookes->count()+$courseBookeAlls->count()==0)
                                <input type="time" name="kursstartterminTime" id="kursstartterminTime" class="form-input-date"
                                       @if(isset($kursstartterminTime))
                                           value="{{ $kursstartterminTime }}"
                                       @else
                                           value="{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('H:i') }}"
                                       @endif
                                       min="{{ $timeMin }}" max="{{ $timeMax }}"
                                >
                               </div>
                               <br>
                                <div class="form-field">
                                    <label for="kurslaenge" class="form-label">{{ $organiser->veranstaltung }} können im Zeitfenster zu buchen:</label>
                                    <div class="form-input-text">
                                        {{ $timeMin }} - {{ $timeMax }}
                                    </div>

                               @else
                                    <div class="form-input-text">
                                        {{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('H:i') }}
                                    </div>>
                                @endif
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="kurslaenge" class="form-label">Kursdauer:</label>
                            <div class="form-input-text">
                                {{ Illuminate\Support\Carbon::parse($coursedate->kurslaenge)->format('H:i') }}
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">{{ $courseBookes->count() }} gebucht(e) Teilnehmer / {{ $courseBookes->count()+$courseBookeAlls->count() }} belegt(e) Plätz(e) / {{ $sportgeraetanzahlMax }} frei(e) Plätz(e)</label>
                            <div class="form-box">
                                @if($sportgeraetanzahlMax>0)
                                    <a href="{{ route('courseBooking.course.book' ,
                                        [
                                           'coursedateId'     => $coursedate->id
                                        ] ) }}"
                                    >
                                        <box-icon name='user-plus'></box-icon>
                                    </a>
                                @endif
                                @foreach($courseBookes as $courseBook)
                                    <a href="{{ route('courseBooking.course.destroyBooked' ,
                                        [
                                            'courseBookId'  => $courseBook->id,
                                            'coursedateId'  => $coursedate->id
                                        ]
                                        ) }}"
                                    >
                                        <span class="form-button"><box-icon name='user-minus'></box-icon>
                                            {{ $loop->iteration }} {{ $courseBook->participant->name}}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">{{ $courseBookeAlls->count() }}  anderer Teilnehmer</label>
                            <div class="form-box">
                                @foreach($courseBookeAlls as $courseBookAll)
                                       <span class="form-button-fix">
                                            {{ $loop->iteration+$courseBookes->count() }} Teilnehmer
                                        </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="trainer_id" class="form-label">{{ $organiser->trainerUeberschrift }}:</label>
                            <div class="form-input-text">
                                @foreach($coursedate->users as $user)
                                    {{ $user->vorname }} {{ $user->nachname }}<br>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-footer">
                    <a href="{{ route('courseBooking.course.index') }}" class="form-button">
                        Zurück
                    </a>
                  @if($courseBookes->count()+$courseBookeAlls->count()==0)
                    <button type="submit" class="form-button">
                        Eintragen
                    </button>
                  @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
