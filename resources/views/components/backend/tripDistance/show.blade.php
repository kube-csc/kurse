<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            Fahrtenbuch – Distanz eintragen
        </h2>
        <div class="dasboard-iconbox">
            <a class="dasboard-iconbox-a"
               href="{{ route('backend.tripDistance.index', ['all_courses' => $showAll ? 1 : 0]) }}"
               title="Zurück zur Kursauswahl">
                <box-icon name='arrow-back'></box-icon>
            </a>
        </div>
    </x-slot>

    {{-- ── Kursdistanz + Checkbox-Auswahl wer übernimmt ── --}}
    <div class="main-box mt-4">
        <div class="box">
            <form action="{{ route('backend.tripDistance.updateCoursedateDistance', $coursedate->id) }}"
                  method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="all_courses" value="{{ $showAll ? 1 : 0 }}">

                <div class="form-group">
                    <div class="form-card">

                        <div class="form-field">
                            <label class="form-label">Kurs</label>
                            <div class="form-input-text">{{ $coursedate->course->kursName ?? '–' }}</div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Termin</label>
                            <div class="form-input-text">
                                {{ date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) }}
                                – {{ date('H:i', strtotime($coursedate->kursendtermin)) }} Uhr
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="kursFahrtenlaenge" class="form-label">
                                Distanz (km)
                            </label>
                            <input id="kursFahrtenlaenge"
                                   type="text"
                                   name="kursFahrtenlaenge"
                                   value="{{ number_format((float) $coursedate->kursFahrtenlaenge, 2, ',', '') }}"
                                   class="form-input"
                                   placeholder="0,00">
                            @error('kursFahrtenlaenge')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4">
                        <label class="form-label mb-2 block" for="select_all_distribution">
                            Auswahl uebernehmen
                        </label>
                        <div class="flex items-center gap-3 py-2 border-b border-gray-100 dark:border-gray-700">
                            <input type="checkbox"
                                   id="select_all_distribution"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Alle Trainer und Teilnehmer markieren</span>
                        </div>
                    </div>

                    {{-- Trainer-Checkboxen --}}
                    @if($coursedate->users->isNotEmpty())
                        <div class="mt-6">
                            <label class="form-label mb-2 block">
                                {{ $organiser->trainerUeberschrift }} – Kursdistanz übernehmen:
                            </label>
                            @foreach($coursedate->users as $trainer)
                                @php $trainerKm = (float) ($trainer->pivot->trainerFahrtenlaenge ?? 0); @endphp
                                <div class="flex items-center gap-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <input type="checkbox"
                                           name="selected_trainers[]"
                                           value="{{ $trainer->id }}"
                                           @checked($trainerKm == 0)
                                           class="distribution-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200 w-48 shrink-0">
                                        {{ $trainer->vorname }} {{ $trainer->nachname }}
                                    </span>
                                    <span class="text-sm text-gray-400 shrink-0">
                                        aktuell: {{ number_format($trainerKm, 2, ',', '') }} km
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Teilnehmer-Checkboxen (nur erste Buchung je Person) --}}
                    @if($uniqueBookings->isNotEmpty())
                        <div class="mt-6">
                            <label class="form-label mb-2 block">
                                Teilnehmer – Kursdistanz übernehmen:
                            </label>
                            @foreach($uniqueBookings as $booking)
                                @php
                                    $pName = trim(($booking->participant->vorname ?? '') . ' ' . ($booking->participant->nachname ?? ''));
                                    if ($pName === '')          { $pName = $booking->participant->name ?? null; }
                                    if (!$pName && $booking->trainer_id)  { $pName = 'Trainer #'   . $booking->trainer_id; }
                                    if (!$pName && $booking->mitglied_id) { $pName = 'Mitglied #'  . $booking->mitglied_id; }
                                    if (!$pName)                { $pName = 'Teilnehmer #' . $booking->id; }
                                    $pKm = (float) $booking->teilnehmerFahrtenlaenge;
                                @endphp
                                <div class="flex items-center gap-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <input type="checkbox"
                                           name="selected_participants[]"
                                           value="{{ $booking->id }}"
                                           @checked($pKm == 0)
                                           class="distribution-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-200 w-48 shrink-0">
                                        {{ $pName }}
                                    </span>
                                    <span class="text-sm text-gray-400 shrink-0">
                                        aktuell: {{ number_format($pKm, 2, ',', '') }} km
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>

                <div class="form-footer">
                    <a href="{{ route('backend.tripDistance.index', ['all_courses' => $showAll ? 1 : 0]) }}"
                       class="form-button">
                        {{ __('main.back') }}
                    </a>
                    <button type="submit" class="form-button">
                        Speichern &amp; an Markierte verteilen
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Trainer – individuell überschreiben ── --}}
    @if($coursedate->users->isNotEmpty())
        <div class="main-box mt-4">
            <div class="box">
                <div class="form-group">
                    <label class="form-label mb-2 block">
                        {{ $organiser->trainerUeberschrift }} – individuell überschreiben
                    </label>
                    @foreach($coursedate->users as $trainer)
                        <form action="{{ route('backend.tripDistance.updateTrainerDistance',
                                  ['coursedate' => $coursedate->id, 'userId' => $trainer->id]) }}"
                              method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="all_courses" value="{{ $showAll ? 1 : 0 }}">
                            <div class="flex items-center gap-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-sm text-gray-700 dark:text-gray-200 w-48 shrink-0">
                                    {{ $trainer->vorname }} {{ $trainer->nachname }}
                                </span>
                                <input type="text"
                                       name="trainerFahrtenlaenge"
                                       value="{{ number_format((float) ($trainer->pivot->trainerFahrtenlaenge ?? $coursedate->kursFahrtenlaenge), 2, ',', '') }}"
                                       class="form-input max-w-[120px]"
                                       placeholder="0,00">
                                <span class="text-sm text-gray-500 dark:text-gray-400 shrink-0">km</span>
                                <button type="submit" class="form-button shrink-0">Speichern</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ── Teilnehmer – individuell überschreiben (nur erste Buchung je Person) ── --}}
    @if($uniqueBookings->isNotEmpty())
        <div class="main-box mt-4">
            <div class="box">
                <div class="form-group">
                    <label class="form-label mb-2 block">
                        Teilnehmer – individuell überschreiben
                    </label>
                    @foreach($uniqueBookings as $booking)
                        @php
                            $pName = trim(($booking->participant->vorname ?? '') . ' ' . ($booking->participant->nachname ?? ''));
                            if ($pName === '')          { $pName = $booking->participant->name ?? null; }
                            if (!$pName && $booking->trainer_id)  { $pName = 'Trainer #'   . $booking->trainer_id; }
                            if (!$pName && $booking->mitglied_id) { $pName = 'Mitglied #'  . $booking->mitglied_id; }
                            if (!$pName)                { $pName = 'Teilnehmer #' . $booking->id; }
                        @endphp
                        <form action="{{ route('backend.tripDistance.updateParticipantDistance', $booking->id) }}"
                              method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="all_courses" value="{{ $showAll ? 1 : 0 }}">
                            <div class="flex items-center gap-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-sm text-gray-700 dark:text-gray-200 w-48 shrink-0">
                                    {{ $pName }}
                                </span>
                                <input type="text"
                                       name="teilnehmerFahrtenlaenge"
                                       value="{{ number_format((float) $booking->teilnehmerFahrtenlaenge, 2, ',', '') }}"
                                       class="form-input max-w-[120px]"
                                       placeholder="0,00">
                                <span class="text-sm text-gray-500 dark:text-gray-400 shrink-0">km</span>
                                <button type="submit" class="form-button shrink-0">Speichern</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="main-box mt-4">
            <div class="box">
                <div class="form-group">
                    <p class="form-label">Keine Teilnehmer für diesen Termin gebucht.</p>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>

<script>
    (function () {
        var master = document.getElementById('select_all_distribution');
        if (!master) {
            return;
        }

        var checkboxes = Array.prototype.slice.call(document.querySelectorAll('.distribution-checkbox'));

        function syncMaster() {
            if (checkboxes.length === 0) {
                master.checked = false;
                master.indeterminate = false;
                return;
            }

            var checkedCount = checkboxes.filter(function (cb) { return cb.checked; }).length;

            master.checked = checkedCount === checkboxes.length;
            master.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
        }

        master.addEventListener('change', function () {
            checkboxes.forEach(function (cb) {
                cb.checked = master.checked;
            });
            syncMaster();
        });

        checkboxes.forEach(function (cb) {
            cb.addEventListener('change', syncMaster);
        });

        syncMaster();
    })();
</script>




