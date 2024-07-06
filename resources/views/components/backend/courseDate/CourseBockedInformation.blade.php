<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                {{ __('backend.Participant Booked') }}
            </h2>
        </div>
    </x-slot>
    <div class="main-box">
        <div class="dashboard-flexbox">
            @foreach($courseParticipantBookeds as $courseParticipantBooked)

                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        @if($courseParticipantBooked->participant_id>0)
                            <label class="label">Vorname:</label>
                            {{$courseParticipantBooked->participant->vorname }}<br>
                            <label class="label">Nachname:</label>
                            {{$courseParticipantBooked->participant->nachname }}<br>
                            <label class="label">Telefon:</label>
                            {{$courseParticipantBooked->participant->telefon }}<br>
                            <label class="label">E-Mail:</label>
                            {{$courseParticipantBooked->participant->email }}<br>
                            @if($courseParticipantBooked->participant->nachricht != null)
                                <label class="label">Nachricht:</label>
                                <p>
                                  {!! $courseParticipantBooked->participant->nachricht !!}
                                </p>
                            @endif
                        @endif
                        @if($courseParticipantBooked->trainer_id>0)
                               <label class="label">Teilnehmer gebucht von:</label>
                               {{$courseParticipantBooked->trainer->vorname }} {{$courseParticipantBooked->trainer->nachname }}<br>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
        <div class="form-footer">
            <a href="{{ route('backend.courseDate.index') }}" class="form-button">
                {{ __('main.back') }}
            </a>
        </div>
    </div>

</x-app-layout>


