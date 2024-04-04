<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            {{ __('backend.Date Management') }} {{ $organiser->veranstaltung }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.sporting equipment help') !!}
                </p>
            </div>
        </div>
    </x-slot>
    <div class="main-box">
        <div class="box">

            <form action="{{ route('backend.courseDate.updateBookFirst', $coursedate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <div class="form-card">
                        <div class="form-field">
                            <label for="kursstarttermin" class="form-label">Start Datum:</label>
                            <div class="form-field flex">
                                <div class="form-input-text">
                                   {{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('d.m.Y') }}
                                </div>
                                @if($courseBookes->count()==0 and $timeMin!=$timeMax)
                                <input type="time" name="kursstartterminTime" id="kursstartterminTime" class="form-input-date"
                                       value=
                                      @if(isset($kursstartterminTime))
                                           "{{ $kursstartterminTime }}"
                                       @else
                                           "{{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('H:i') }}"
                                       @endif
                                       min="{{ $timeMin }}" max="{{ $timeMax }}"
                                >
                            </div>
                            <br>
                            <div class="form-field">
                                 <label for="kurslaenge" class="form-label">Die Startzeit können im Zeitfenster geändert werden:</label>
                                 <div class="form-input-text">
                                      {{ $timeMin }} Uhr - {{ $timeMax }} Uhr
                                 </div>
                              @else
                              <div class="form-input-text">
                                 {{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('H:i') }} Uhr
                              </div>
                              @endif
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="kursstarttermin" class="form-label">letztmögliches Ende::</label>
                            <div class="form-box">
                                {{ Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('d.m.Y') }}
                                {{ Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('H:i') }} Uhr
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="trainer_id" class="form-label">Trainer:</label>
                            <div class="form-box">
                                @foreach($trainers as $trainer)
                                    {{ $trainer->vorname }} {{ $trainer->nachname }}<br>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">Name des Termins:</label>
                            <div class="form-box">
                               {{ $course->kursName }}
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">{{ $courseBookes->count() }} belegt(e) Plätz(e) in Sportgerät(e) / {{ $sportgeraetanzahlMax }} frei(e) Plätz(e):</label>
                            <div class="form-box">
                                @if($sportgeraetanzahlMax>0 and ($courseBookes->count()>0 or $timeMin==$timeMax))
                                    <a href="{{ route('backend.courseDate.book' ,
                                        [
                                           'coursedateId'     => $coursedate->id
                                        ] ) }}"
                                    >
                                        <box-icon name='user-plus'></box-icon>
                                    </a>
                                @endif
                                @foreach($courseBookes as $courseBook)
                                    <a href="{{ route('backend.courseDate.destroyBooked' ,
                                        [
                                            'courseBookId'  => $courseBook->id,
                                            'coursedateId'  => $coursedate->id
                                        ]
                                        ) }}"
                                    >
                                      <span class="form-button"><box-icon name='user-minus'></box-icon>
                                          {{ $loop->iteration }}
                                          @if($courseBook->participant_id > 0)
                                             {{ $courseBook->participant->name}}
                                          @else
                                             Teilnehmer
                                          @endif
                                      </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">{{ $sportEquipmentFrees->count() }} freie(s) Sportgerät(e):</label>
                                <div class="form-box">
                                   @foreach($sportEquipmentFrees as $sportEquipmentFree)
                                    <a href="{{ route('backend.courseDate.equipmentBooked' ,
                                    [
                                        'coursedateId'     => $coursedate->id,
                                        'sportequipmentId' => $sportEquipmentFree->id
                                    ] ) }}"
                                    >
                                        <span class="form-button">
                                            <box-icon name='plus-circle'></box-icon>
                                            {{ $sportEquipmentFree->sportgeraet }}
                                        </span>
                                    </a>
                                   @endforeach
                               </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">{{ $sportEquipmentKursBookeds->count() }} belegt(e) Sportgerät(e) im Termin:</label>
                            <div class="form-box">
                                @foreach($sportEquipmentKursBookeds as $sportEquipmentKursBooked)
                                    <a href="{{ route('backend.courseDate.equipmentBookedDestroy' ,
                                    [
                                        'coursedateId'     => $coursedate->id,
                                        'kursId'           => $sportEquipmentKursBooked->kurs_id,
                                        'sportgeraet'      => $sportEquipmentKursBooked->sportgeraet
                                    ] ) }}"
                                    >
                                    <span class="form-button">
                                        <box-icon name='minus-circle'></box-icon>
                                        {{ $sportEquipmentKursBooked->sportgeraet}}
                                    </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">{{ $sportEquipmentBookeds->count() }} belegt(e) Sportgerät(e) in anderen Termin:</label>
                            <div class="form-box">
                                @foreach($sportEquipmentBookeds as $sportEquipmentBooked)
                                    <span>
                                        {{ $sportEquipmentBooked->sportgeraet}} /
                                        {{ $sportEquipmentBooked->vorname }} {{ $sportEquipmentBooked->nachname }}
                                    </span><br>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">{{ $teilnehmerKursBookeds->count() }} Teilnehmer in andere Termin(e):</label>
                            <div class="form-box">
                                @foreach($teilnehmerKursBookeds as $teilnehmerKursBooked)
                                    <span>
                                        {{ $loop->iteration }} Teilnehmer /
                                        {{ $teilnehmerKursBooked->vorname }} {{ $teilnehmerKursBooked->nachname }}
                                    </span><br>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('backend.courseDate.index') }}" class="form-button">
                        {{ __('main.back') }}
                    </a>
                    @if($courseBookes->count()==0 and $timeMin<>$timeMax)
                        <button type="submit" class="form-button">
                            {{ __('main.save') }}
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
