<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\CoursedateHelper;
use App\Http\Controllers\Controller;
use App\Mail\BadWeatherParticipantMail;
use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BadWeatherCancellationController extends Controller
{
    public function edit(Coursedate $coursedate)
    {
        $organiser = $this->organiser();

        if (!$this->canManageCoursedate($coursedate, $organiser->id)) {
            self::warning('Du darfst diesen Termin nicht bearbeiten.');
            return redirect()->route('backend.courseDate.index');
        }

        $trainerMessageSuggestion = $this->buildTrainerMessageSuggestion();

        return view('components.backend.BadWeatherCancellation.edit', compact('coursedate', 'organiser', 'trainerMessageSuggestion'));
    }

    public function update(Request $request, Coursedate $coursedate)
    {
        $organiser = $this->organiser();

        if (!$this->canManageCoursedate($coursedate, $organiser->id)) {
            self::warning('Du darfst diesen Termin nicht bearbeiten.');
            return redirect()->route('backend.courseDate.index');
        }

        $validated = $request->validate([
            'action_type' => ['required', 'in:postpone,cancel'],
            'new_start_date' => ['required_if:action_type,postpone', 'date'],
            'new_start_time' => ['required_if:action_type,postpone', 'date_format:H:i'],
            'kurs_information' => ['nullable', 'string', 'max:2000'],
            'trainer_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldStart = Carbon::parse($coursedate->kursstarttermin);
        $oldEnd = Carbon::parse($coursedate->kursendtermin);
        $kursInformation = trim((string) ($validated['kurs_information'] ?? ''));
        $trainerMessage = trim((string) ($validated['trainer_message'] ?? ''));
        if ($trainerMessage === '') {
            $trainerMessage = $this->buildTrainerMessageSuggestion();
        }

        $updatePayload = [
            'kursInformation' => $kursInformation,
            'bearbeiter_id' => Auth::id(),
        ];

        if ($validated['action_type'] === 'postpone') {
            $newStart = Carbon::parse($validated['new_start_date'].' '.$validated['new_start_time']);
            $duration = Carbon::parse($coursedate->kurslaenge);
            $newEnd = (clone $newStart)->addHours($duration->hour)->addMinutes($duration->minute);

            $maxParticipant = $this->getMaxParticipantsForNewTimeslot($coursedate, $newStart, $newEnd);
            $bookedParticipantCount = CourseParticipantBooked::query()
                ->where('kurs_id', $coursedate->id)
                ->whereNotNull('participant_id')
                ->count();

            if ($bookedParticipantCount > $maxParticipant) {
                self::warning('Verschiebung nicht möglich: Für den neuen Zeitpunkt sind nicht genug Plätze für alle bereits gebuchten Teilnehmer verfügbar.');
                return redirect()->back()->withInput();
            }

            $updatePayload = array_merge($updatePayload, [
                'kursstarttermin' => $newStart,
                'kursendtermin' => $newEnd,
                'kursstartvorschlag' => $newStart,
                'kursendvorschlag' => $newEnd,
                'kursstartvorschlagkunde' => $newStart,
                'kursendvorschlagkunde' => $newEnd,
            ]);
        } else {
            $updatePayload['kursNichtDurchfuerbar'] = true;
        }

        $coursedate->update($updatePayload);

        $this->notifyParticipants(
            $coursedate,
            $oldStart,
            $oldEnd,
            $validated['action_type'],
            $trainerMessage
        );

        self::success(
            $validated['action_type'] === 'postpone'
                ? 'Der Termin wurde verschoben und Teilnehmer wurden informiert.'
                : 'Der Termin wurde abgesagt und Teilnehmer wurden informiert.'
        );

        return redirect()->route('backend.courseDate.index');
    }

    private function canManageCoursedate(Coursedate $coursedate, int $organiserId): bool
    {
        if ((int) $coursedate->organiser_id !== $organiserId) {
            return false;
        }

        if (Auth::guard('admin')->check()) {
            return true;
        }

        return $coursedate->users()->where('users.id', Auth::id())->exists();
    }

    private function getMaxParticipantsForNewTimeslot(Coursedate $coursedate, Carbon $newStart, Carbon $newEnd): int
    {
        $simulatedCoursedate = clone $coursedate;
        $simulatedCoursedate->kursstarttermin = $newStart;
        $simulatedCoursedate->kursendtermin = $newEnd;

        $sportgeraetanzahlMax = CoursedateHelper::sportgeraetanzahlMaxPlaetze($coursedate->organiser_id);
        $overlapStats = CoursedateHelper::getOverlapBookingStats($simulatedCoursedate);
        $maxParticipant = $sportgeraetanzahlMax - $overlapStats->sum('max');

        if ((int) $coursedate->sportgeraetanzahl > 0) {
            $maxParticipant = min($maxParticipant, (int) $coursedate->sportgeraetanzahl);
        }

        return max(0, (int) $maxParticipant);
    }

    private function notifyParticipants(
        Coursedate $coursedate,
        Carbon $oldStart,
        Carbon $oldEnd,
        string $actionType,
        string $trainerMessage
    ): void {
        $participantBookeds = CourseParticipantBooked::query()
            ->with('participant')
            ->where('kurs_id', $coursedate->id)
            ->whereNotNull('participant_id')
            ->get();

        $participants = $participantBookeds
            ->pluck('participant')
            ->filter()
            ->unique('id');

        foreach ($participants as $participant) {
            Mail::to($participant->email)->send(new BadWeatherParticipantMail(
                $coursedate,
                $participant,
                $oldStart,
                $oldEnd,
                $actionType,
                $trainerMessage
            ));
        }
    }

    private function buildTrainerMessageSuggestion(): string
    {
        return "Liebe Teilnehmerinnen und Teilnehmer,\naufgrund der aktuellen Wetterlage müssen wir den Termin anpassen.\nBitte prüft den neuen Termin im Buchungsbereich.\nWenn der Termin für euch nicht in Frage kommt, könnt ihr eure Buchung stornieren oder einen anderen Termin buchen.\nVielen Dank für euer Verständnis.\n\nSportliche Grüße\nEuer Trainerteam";
    }
}
