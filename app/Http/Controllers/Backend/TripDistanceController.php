<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TripDistanceController extends Controller
{
    /**
     * Schritt 1: Liste der Kurstermine zur Auswahl.
     */
    public function index(Request $request): View
    {
        $organiser  = $this->organiser();
        $showAll    = $request->boolean('all_courses');
        $authUserId = Auth::id();

        $query = Coursedate::query()
            ->where('organiser_id', $organiser->id)
            ->where('kursendtermin', '>=', date('Y-m-d'))
            ->with(['course:id,kursName'])
            ->orderBy('kursstarttermin');

        if (!$showAll) {
            $query->where(function ($q) use ($authUserId) {
                $q->whereHas('users', function ($u) use ($authUserId) {
                    $u->where('users.id', $authUserId);
                })->orWhereHas('courseParticipantBookeds', function ($b) use ($authUserId) {
                    $b->where('participant_id', $authUserId)
                      ->orWhere('mitglied_id', $authUserId)
                      ->orWhere('trainer_id', $authUserId);
                });
            });
        }

        $coursedates = $query->get();

        return view('components.backend.tripDistance.index', compact('coursedates', 'showAll'));
    }

    /**
     * Schritt 2: Distanz-Eingabe für einen ausgewählten Kurstermin.
     * Pro Person wird nur die erste Buchung (niedrigste ID) angezeigt –
     * jede Person kann die Strecke nur einmal fahren.
     */
    public function show(Request $request, Coursedate $coursedate): View
    {
        if (!$this->belongsToCurrentOrganiser($coursedate)) {
            abort(403);
        }

        $showAll = $request->boolean('all_courses');

        $coursedate->load([
            'course:id,kursName',
            'users:id,vorname,nachname',
            'courseParticipantBookeds' => function ($q) {
                $q->select([
                    'id', 'kurs_id', 'participant_id',
                    'trainer_id', 'mitglied_id', 'teilnehmerFahrtenlaenge',
                ])->orderBy('id'); // älteste Buchung zuerst
            },
            'courseParticipantBookeds.participant:id,name,vorname,nachname',
        ]);

        // Nur die erste Buchung je eindeutiger Person behalten.
        // Gleiche participant_id / mitglied_id / trainer_id = gleiche Person.
        $uniqueBookings = $coursedate->courseParticipantBookeds
            ->unique(function ($booking) {
                if ($booking->participant_id) {
                    return 'participant_' . $booking->participant_id;
                }
                if ($booking->mitglied_id) {
                    return 'mitglied_' . $booking->mitglied_id;
                }
                if ($booking->trainer_id) {
                    return 'trainer_' . $booking->trainer_id;
                }
                return 'anon_' . $booking->id; // anonyme Buchung bleibt eindeutig
            })
            ->values();

        $organiser = $this->organiser();

        return view('components.backend.tripDistance.show',
            compact('coursedate', 'showAll', 'organiser', 'uniqueBookings'));
    }

    /**
     * Kursdistanz speichern und auf alle per Checkbox markierten Trainer/Teilnehmer verteilen.
     */
    public function updateCoursedateDistance(Request $request, Coursedate $coursedate): RedirectResponse
    {
        if (!$this->belongsToCurrentOrganiser($coursedate)) {
            abort(403);
        }

        $request->validate([
            'kursFahrtenlaenge' => ['required', 'regex:/^\d+(?:[\.,]\d{1,2})?$/'],
        ]);

        $distance              = $this->normalizeDistance($request->input('kursFahrtenlaenge'));
        $selectedTrainers      = array_filter((array) $request->input('selected_trainers', []));
        $selectedParticipants  = array_filter((array) $request->input('selected_participants', []));

        DB::transaction(function () use ($coursedate, $distance, $selectedTrainers, $selectedParticipants) {
            $coursedate->update([
                'kursFahrtenlaenge' => $distance,
                'bearbeiter_id'     => Auth::id(),
            ]);

            if (!empty($selectedParticipants)) {
                CourseParticipantBooked::where('kurs_id', $coursedate->id)
                    ->whereIn('id', $selectedParticipants)
                    ->update(['teilnehmerFahrtenlaenge' => $distance]);
            }

            if (!empty($selectedTrainers)) {
                DB::table('coursedate_user')
                    ->where('coursedate_id', $coursedate->id)
                    ->whereIn('user_id', $selectedTrainers)
                    ->update(['trainerFahrtenlaenge' => $distance, 'updated_at' => now()]);
            }
        });

        $count = count($selectedTrainers) + count($selectedParticipants);
        self::success("Die Kursdistanz wurde gespeichert und auf {$count} markierte Person(en) verteilt.");

        return redirect()->route('backend.tripDistance.show', [
            'coursedate'  => $coursedate->id,
            'all_courses' => $request->boolean('all_courses') ? 1 : 0,
        ]);
    }

    /**
     * Individuelle Teilnehmerdistanz überschreiben.
     */
    public function updateParticipantDistance(Request $request, CourseParticipantBooked $courseParticipantBooked): RedirectResponse
    {
        if (!$this->belongsToCurrentOrganiser(Coursedate::findOrFail($courseParticipantBooked->kurs_id))) {
            abort(403);
        }

        $request->validate([
            'teilnehmerFahrtenlaenge' => ['required', 'regex:/^\d+(?:[\.,]\d{1,2})?$/'],
        ]);

        $courseParticipantBooked->update([
            'teilnehmerFahrtenlaenge' => $this->normalizeDistance($request->input('teilnehmerFahrtenlaenge')),
        ]);

        self::success('Die individuelle Teilnehmerdistanz wurde gespeichert.');

        return redirect()->route('backend.tripDistance.show', [
            'coursedate'  => $courseParticipantBooked->kurs_id,
            'all_courses' => $request->boolean('all_courses') ? 1 : 0,
        ]);
    }

    /**
     * Individuelle Trainerdistanz in coursedate_user überschreiben.
     */
    public function updateTrainerDistance(Request $request, Coursedate $coursedate, int $userId): RedirectResponse
    {
        if (!$this->belongsToCurrentOrganiser($coursedate)) {
            abort(403);
        }

        $request->validate([
            'trainerFahrtenlaenge' => ['required', 'regex:/^\d+(?:[\.,]\d{1,2})?$/'],
        ]);

        $updated = DB::table('coursedate_user')
            ->where('coursedate_id', $coursedate->id)
            ->where('user_id', $userId)
            ->update([
                'trainerFahrtenlaenge' => $this->normalizeDistance($request->input('trainerFahrtenlaenge')),
                'updated_at'           => now(),
            ]);

        if ($updated === 0) {
            self::warning('Der Trainer ist diesem Termin nicht zugeordnet.');
        } else {
            self::success('Die individuelle Trainerdistanz wurde gespeichert.');
        }

        return redirect()->route('backend.tripDistance.show', [
            'coursedate'  => $coursedate->id,
            'all_courses' => $request->boolean('all_courses') ? 1 : 0,
        ]);
    }

    private function belongsToCurrentOrganiser(Coursedate $coursedate): bool
    {
        return (int) $coursedate->organiser_id === (int) $this->organiserDomainId();
    }

    private function normalizeDistance(string $value): float
    {
        return round((float) str_replace(',', '.', trim($value)), 2);
    }
}




