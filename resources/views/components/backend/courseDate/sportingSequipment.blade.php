<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('backend.Course Dates') }}   {{  __('backend.sporting quipment') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!!  __('backend.sporting quipment help') !!}
                </p>
            </div>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!--Temp: Fehlermeldung anzeigen wird nicht benutz-->
        @if(isset($danger))
            <div class="alert alert-danger mb-5 mt-1">
                {{ $danger }}
            </div>
        @endif
        <div class="box">
                <div class="form-group ">
                    <div class="form-card ">
                        <div class="form-field ">
                            <label for="kursstarttermin" class="form-label">Start Datum</label>
                            <div class="form-field flex text">
                                {{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('d-m-Y') }}
                                {{ Illuminate\Support\Carbon::parse($coursedate->kursstarttermin)->format('H:i') }} Uhr
                            </div>
                        </div>
                        <div class="form-field ">
                            <label for="kursstarttermin" class="form-label">End Datum</label>
                            <div class="form-field flex text">
                                {{ Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('d-m-Y') }}
                                {{ Illuminate\Support\Carbon::parse($coursedate->kursendtermin)->format('H:i') }} Uhr
                            </div>
                        </div>

                        <div class="form-field ">
                            <label for="trainer_id" class="form-label">Trainer</label>
                            <div class="form-field flex text">
                                {{ $coursedate->getTrainerName->vorname }} {{ $coursedate->getTrainerName->nachname }}
                            </div>
                        </div>

                        <div class="form-field ">
                            <label for="course_id" class="form-label">Kursname</label>
                            <div class="form-field flex text">
                               {{ $course->kursName }}
                            </div>
                        </div>

                        <div class="form-field ">
                            <label for="course_id" class="form-label">{{ $couseBookes->count() }} Belegte Plätze in Sportgeräte / {{ $sportgeraetanzahlMax }} Teilnehmer feie Plätze</label>
                            <div class="px-4 py-3 bg-gray-300 text-right sm:px-6">
                                @if($sportgeraetanzahlMax>0)
                                    <a href="{{ route('backend.courseDate.Book' ,
                                        [
                                            'sportequipmentId' => 0,
                                            'coursedateId'     => $coursedate->id
                                        ] ) }}"
                                    >
                                        <box-icon name='user-plus'></box-icon>
                                    </a>
                                @endif
                                @foreach($couseBookes as $couseBook)
                                    <a href="{{ route('backend.courseDate.destroyBooked' ,
                                        [
                                            'couseBookId'   => $couseBook->id,
                                            'coursedateId'  => $coursedate->id
                                        ]
                                        ) }}"
                                    >
                                      <button class="form-button"><box-icon name='user-minus'></box-icon>
                                            {{ $loop->iteration }} Teilnehmer
                                      </button>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-field ">
                            <label for="course_id" class="form-label">{{ $sportEquipments->count() }} freie Sportgeräte</label>
                                <div class="px-4 py-3 bg-gray-300 text-right sm:px-6">
                                   @foreach($sportEquipments as $sportEquipment)
                                    <a href="{{ route('backend.courseDate.Book' ,
                                    [
                                        'sportequipmentId' => $sportEquipment->id,
                                        'coursedateId'     => $coursedate->id
                                    ] ) }}"
                                    >
                                        <button class="form-button">{{ $sportEquipment->sportgeraet }}</button>
                                    </a>
                                   @endforeach
                               </div>
                        </div>

                        <div class="form-field ">
                            <label for="course_id" class="form-label">{{ $sportEquipmentBookeds->count() }} belegte Sportgeräte</label>
                            <div class="px-4 py-3 bg-gray-300 text-right sm:px-6">
                                @foreach($sportEquipmentBookeds as $sportEquipmentBooked)
                                    <span class="form-text">
                                        {{ $sportEquipmentBooked->sportgeraet}} /
                                        {{ $sportEquipmentBooked->vorname }} /
                                        {{ $sportEquipmentBooked->nachname }}

                                    </span><br>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-300 text-right sm:px-6">
                    <a href="{{ route('backend.courseDate.index') }}" class="form-button">
                        Zurück
                    </a>
                </div>

        </div>
    </div>
</x-app-layout>
