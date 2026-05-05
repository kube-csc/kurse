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
                                @if($courseBookes->count() == 0 && $timeMin != $timeMax)
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
                                 <label for="kurslaenge" class="form-label">Die Startzeit kann im folgenden Zeitfenster geändert werden:</label>
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
                            <label for="kursstarttermin" class="form-label">Letztmögliches Ende:</label>
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
                            <label for="course_id" class="form-label">
                                {{ $courseBookes->count() }}
                                {{ $courseBookes->count() === 1 ? 'belegter Platz' : 'belegte Plätze' }}
                                in
                                {{ $organiser->materialUeberschrift }} /
                                {{ $sportgeraetanzahlMax }}
                                {{ $sportgeraetanzahlMax === 1 ? 'freier Platz' : 'freie Plätze' }}:
                            </label>
                            <div class="form-box">
                                @php
                                    $canBookParticipant = $sportgeraetanzahlMax > 0
                                        && (($courseBookes->count() > 0 && $poolHasRemainingPlace) || $timeMin == $timeMax);
                                @endphp
                                @if($canBookParticipant)
                                    <a href="{{ route('backend.courseDate.book' ,
                                        [
                                           'coursedateId'     => $coursedate->id
                                        ] ) }}"
                                    >
                                        <box-icon name='user-plus'></box-icon>
                                    </a>
                                @endif
                                @foreach($courseBookes as $courseBook)
                                    @php
                                        $participantFullName = trim(
                                            (($courseBook->participant->vorname ?? '') . ' ' . ($courseBook->participant->nachname ?? ''))
                                        );
                                        if ($participantFullName === '' && isset($courseBook->participant)) {
                                            $participantFullName = $courseBook->participant->name ?? 'Teilnehmer';
                                        }
                                    @endphp
                                    <a href="{{ route('backend.courseDate.destroyBooked' ,
                                        [
                                            'courseBookId'  => $courseBook->id,
                                            'coursedateId'  => $coursedate->id
                                        ]
                                        ) }}"
                                       onclick="return confirm('Sind Sie sicher, dass Sie diesen {{ $courseBook->participant_id > 0 ? $participantFullName : 'Teilnehmer' }} löschen möchten?')"
                                    >
                                      <span class="form-button"><box-icon name='user-minus'></box-icon>
                                          {{ $loop->iteration }}
                                          @if($courseBook->participant_id > 0)
                                             {{ $participantFullName }}
                                          @else
                                             Teilnehmer
                                          @endif
                                      </span>
                                    </a>
                                @endforeach
                            </div>

                            <div x-data="{ showEquipmentInfo: false }" style="margin-top: 6px;">
                                <button
                                    type="button"
                                    class="form-button"
                                    style="padding: 6px 10px; font-size: 0.9em;"
                                    @click="showEquipmentInfo = !showEquipmentInfo"
                                    :aria-expanded="showEquipmentInfo.toString()"
                                >
                                    <span x-show="!showEquipmentInfo">Details anzeigen</span>
                                    <span x-show="showEquipmentInfo" x-cloak>Details ausblenden</span>
                                </button>

                                <div x-show="showEquipmentInfo" x-cloak x-transition.opacity style="margin-top: 8px;">
                                    <div class="form-input-text" style="margin-top: 8px;">
                                        maximale Plätze vom Termin = {{ $coursedate->sportgeraetanzahl ?? 'n/a' }}
                                    </div>
                                    <div class="form-input-text" style="margin-top: 8px;">
                                        Reservierte Plätze für den Termin = {{ $coursedate->sportgeraeteReserviert ?? 'n/a' }}
                                    </div>
                                    <div class="form-input-text" style="margin-top: 8px;">
                                        Benötigte Plätze für gebuchte Teilnehmer = {{ $courseBookes->count()  ?? 'n/a' }}
                                    </div>
                                    <div class="form-input-text" style="margin-top: 8px;">
                                        Gebuchte {{ $organiser->materialUeberschrift }} = {{ $sportEquipmentKursBookeds->count()  ?? 'n/a' }}
                                    </div>
                                    <div class="form-input-text" style="margin-top: 8px;">
                                        Gebuchte Plätze in {{ $organiser->materialUeberschrift }} = {{ $kursBookedSum ?? 'n/a' }}
                                    </div>
                                    <div class="form-input-text" style="margin-top: 8px;">
                                        Verfügbare {{ $organiser->materialUeberschrift }} (Pool) = {{ $sportEquipmentPool->count()  ?? 'n/a' }}
                                    </div>
                                    <div class="form-input-text" style="margin-top: 8px;">
                                        Verfügbare Plätze (Pool) = {{ $freeSportEquipmentSum  ?? 'n/a' }}
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">
                                {{ $sportEquipmentPool->count() }}
                                {{ $organiser->materialUeberschrift }} im Pool:
                            </label>
                            <div class="form-box">
                                @php $prevPoolSportleranzahl = null; @endphp
                                @foreach($sportEquipmentPool->sortByDesc('sportleranzahl') as $sportEquipmentFree)
                                    @if($prevPoolSportleranzahl !== $sportEquipmentFree->sportleranzahl)
                                        <div class="font-bold mt-1.5">
                                            {{ $sportEquipmentFree->sportleranzahl }}
                                            {{ $sportEquipmentFree->sportleranzahl == 1 ? 'Platz' : 'Plätze' }}:
                                        </div>
                                        @php $prevPoolSportleranzahl = $sportEquipmentFree->sportleranzahl; @endphp
                                    @endif
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
                            <label for="course_id" class="form-label">
                                {{ $sportEquipmentKursBookeds->count() }}
                                {{ $sportEquipmentKursBookeds->count() === 1 ? 'belegtes' : 'belegte' }} {{ $organiser->materialUeberschrift }} im Termin:
                            </label>
                            <div class="form-box">
                                @php $prevSportleranzahl = null; @endphp
                                @foreach($sportEquipmentKursBookeds->sortBy('sportleranzahl')->sortByDesc('sportleranzahl') as $sportEquipmentKursBooked)
                                    @if($prevSportleranzahl !== $sportEquipmentKursBooked->sportleranzahl)
                                        <div class="font-bold mt-1.5">
                                            {{ $sportEquipmentKursBooked->sportleranzahl }}
                                            {{ $sportEquipmentKursBooked->sportleranzahl == 1 ? 'Platz' : 'Plätze' }}:
                                        </div>
                                        @php $prevSportleranzahl = $sportEquipmentKursBooked->sportleranzahl; @endphp
                                    @endif
                                    <a href="{{ route('backend.courseDate.equipmentBookedDestroy' ,
                                    [
                                        'coursedateId'     => $coursedate->id,
                                        'kursId'           => $sportEquipmentKursBooked->kurs_id,
                                        'sportgeraet'      => $sportEquipmentKursBooked->sportgeraet
                                    ] ) }}"
                                    >
                                    <span class="form-button">
                                        <box-icon name='minus-circle'></box-icon>
                                        {{ $sportEquipmentKursBooked->sportgeraet }}
                                    </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">
                                {{ $sportEquipmentBookeds->count() }}
                                {{ $sportEquipmentBookeds->count() === 1 ? 'belegtes' : 'belegte' }} {{ $organiser->materialUeberschrift }} in anderen Terminen:
                            </label>
                            <div class="form-box">
                                @php $sporgeraeteIdVorher = 0; @endphp
                                @foreach($sportEquipmentBookeds as $sportEquipmentBooked)
                                    @php
                                        $bookedVorname = $sportEquipmentBooked->vorname ?? 'ohne Trainer';
                                        $bookedNachname = $sportEquipmentBooked->nachname ?? '';
                                        $isNewEquipment = $sporgeraeteIdVorher != $sportEquipmentBooked->sportgeraet_id;
                                    @endphp
                                    <span>
                                        @if($isNewEquipment)
                                            {{ $sportEquipmentBooked->sportgeraet }}
                                            ({{ $sportEquipmentBooked->sportleranzahl }}
                                            {{ $sportEquipmentBooked->sportleranzahl == 1 ? 'Platz' : 'Plätze' }}) /
                                            {{ trim($bookedVorname . ' ' . $bookedNachname) }}
                                        @else
                                            und {{ trim($bookedVorname . ' ' . $bookedNachname) }}
                                        @endif
                                    </span><br>
                                    @php $sporgeraeteIdVorher = $sportEquipmentBooked->sportgeraet_id; @endphp
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">
                                {{ $overlapingCoursedates->count() }}
                                {{ $overlapingCoursedates->count() === 1 ? 'überlappender Termin' : 'überlappende Termine' }} (inkl. aktuellem Termin):
                            </label>
                            <div class="form-box">
                                @foreach($overlapingCoursedatesWithParticipants as $overlap)
                                    @php
                                        $cd = $overlap['coursedate'] ?? null;
                                    @endphp
                                    <div style="margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                                        <strong>{{ $cd ? Illuminate\Support\Carbon::parse($cd->kursstarttermin)->format('d.m.Y H:i') : '-' }} - {{ $cd ? Illuminate\Support\Carbon::parse($cd->kursendtermin)->format('H:i') : '-' }} Uhr / {{ $cd && $cd->course ? $cd->course->kursName : 'Kurs' }}</strong><br>
                                        Teilnehmer: {{ $overlap['teilnehmerCount'] ?? 0 }} / Reserviert: {{ $overlap['sportgeraeteReserviert'] ?? 0 }} / Maximale Teilnehmer: {{ $cd->sportgeraetanzahl ?? 0 }}<br>
                                        Bedarf an Plätze: {{ $overlap['benoetigtePlaetzeMax'] ?? 0 }} /
                                        Zugewiesen: {{ $overlap['zugewiesenePlaetze'] ?? 0 }} Plätze
                                        ({{ $overlap['zugewieseneSportgeraeteAnzahl'] ?? 0 }} {{ $organiser->materialUeberschrift }})
                                        @if(($overlap['fehlendePlaetze'] ?? 0) > 0)
                                            / Fehlende Plätze: {{ $overlap['fehlendePlaetze'] }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-input-text" style="margin-top: 8px;">
                                Pool Restplätze: {{ $poolRemainingPlaetze ?? 0 }} /
                                Rest-{{ $organiser->materialUeberschrift }}: {{ $poolRemainingSportgeraete ?? 0 }} /
                                @if(!empty($poolHasRemainingPlace))
                                    Es ist noch mindestens ein Platz im Pool vorhanden.
                                @else
                                    Kein freier Platz mehr im Pool.
                                @endif
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="course_id" class="form-label">
                                {{ $teilnehmerKursBookeds->count() }}
                                {{ $teilnehmerKursBookeds->count() === 1 ? 'Teilnehmer in einem anderen Termin' : 'Teilnehmer in anderen Terminen' }}:
                            </label>
                            <div class="form-box">
                                @foreach($teilnehmerKursBookeds as $teilnehmerKursBooked)
                                    <span>
                                        {{ $loop->iteration }} Teilnehmer /
                                        {{ $teilnehmerKursBooked->vorname }} {{ $teilnehmerKursBooked->nachname }}
                                    </span><br>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="kursInformation" class="form-label">Information:</label>
                            <textarea name="kursInformation" id="kursInformation" class="form-input-textarea">{{ old('kursInformation', $coursedate->kursInformation) }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('backend.courseDate.index') }}" class="form-button">
                        {{ __('main.back') }}
                    </a>
                    @if($courseBookes->count() == 0 && $timeMin != $timeMax && $sportgeraetanzahlMax > 0 && $poolHasRemainingPlace)
                        <button type="submit" class="form-button">
                            {{ __('main.save') }}
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
