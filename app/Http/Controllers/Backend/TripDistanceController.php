<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $hasExplicitMonthYear = $request->filled('month') || $request->filled('year');
        [$month, $year] = $this->resolveMonthYear($request);

        $baseQuery = Coursedate::query()
            ->where('organiser_id', $organiser->id)
            ->with(['course:id,kursName']);

        if (!$showAll) {
            $baseQuery->where(function ($q) use ($authUserId) {
                $q->whereHas('users', function ($u) use ($authUserId) {
                    $u->where('users.id', $authUserId);
                })->orWhereHas('courseParticipantBookeds', function ($b) use ($authUserId) {
                    $b->where('participant_id', $authUserId)
                        ->orWhere('mitglied_id', $authUserId)
                        ->orWhere('trainer_id', $authUserId);
                });
            });
        }

        $availableMonths = $this->getAvailableMonths($baseQuery);

        if (!$hasExplicitMonthYear && $availableMonths->isNotEmpty()) {
            $initialMonth = Carbon::create($year, $month, 1, 0, 0, 0);
            $hasCurrentMonthEntries = $availableMonths->contains(function (Carbon $availableMonth) use ($initialMonth) {
                return $availableMonth->isSameMonth($initialMonth);
            });

            if (!$hasCurrentMonthEntries) {
                $lastDate = $availableMonths->last();
                if ($lastDate instanceof Carbon) {
                    $month = $lastDate->month;
                    $year = $lastDate->year;
                }
            }
        }

        $selectedMonth = Carbon::create($year, $month, 1, 0, 0, 0);
        $monthStart = $selectedMonth->copy()->startOfMonth();
        $monthEnd = $selectedMonth->copy()->endOfMonth();
        $prevMonth = $this->findPreviousAvailableMonth($availableMonths, $selectedMonth);
        $nextMonth = $this->findNextAvailableMonth($availableMonths, $selectedMonth);
        $prevYear = $this->findPreviousAvailableYear($availableMonths, $selectedMonth);
        $nextYear = $this->findNextAvailableYear($availableMonths, $selectedMonth);

        $query = (clone $baseQuery)
            ->whereBetween('kursstarttermin', [$monthStart->toDateTimeString(), $monthEnd->toDateTimeString()])
            ->orderBy('kursstarttermin');


        $coursedates = $query->get();

        return view('components.backend.tripDistance.index', [
            'coursedates' => $coursedates,
            'showAll' => $showAll,
            'month' => $month,
            'year' => $year,
            'currentMonthLabel' => $selectedMonth->format('m.Y'),
            'prevMonth' => $this->toNavigationPayload($prevMonth),
            'nextMonth' => $this->toNavigationPayload($nextMonth),
            'prevYear' => $this->toNavigationPayload($prevYear),
            'nextYear' => $this->toNavigationPayload($nextYear),
        ]);
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
        [$month, $year] = $this->resolveMonthYear($request);

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
            compact('coursedate', 'showAll', 'organiser', 'uniqueBookings', 'month', 'year'));
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
            'month' => $request->input('month'),
            'year' => $request->input('year'),
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
            'month' => $request->input('month'),
            'year' => $request->input('year'),
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
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ]);
    }

    private function resolveMonthYear(Request $request): array
    {
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }

        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        return [$month, $year];
    }

    private function getAvailableMonths($baseQuery): Collection
    {
        return (clone $baseQuery)
            ->orderBy('kursstarttermin')
            ->pluck('kursstarttermin')
            ->map(function ($date) {
                return Carbon::parse($date)->startOfMonth();
            })
            ->unique(function (Carbon $date) {
                return $date->format('Y-m');
            })
            ->values();
    }

    private function findPreviousAvailableMonth(Collection $availableMonths, Carbon $selectedMonth): ?Carbon
    {
        $previous = null;

        foreach ($availableMonths as $month) {
            if ($month instanceof Carbon && $month->lt($selectedMonth)) {
                $previous = $month;
            }
        }

        return $previous;
    }

    private function findNextAvailableMonth(Collection $availableMonths, Carbon $selectedMonth): ?Carbon
    {
        foreach ($availableMonths as $month) {
            if ($month instanceof Carbon && $month->gt($selectedMonth)) {
                return $month;
            }
        }

        return null;
    }

    private function findPreviousAvailableYear(Collection $availableMonths, Carbon $selectedMonth): ?Carbon
    {
        $previousYear = $availableMonths
            ->pluck('year')
            ->unique()
            ->filter(function (int $year) use ($selectedMonth) {
                return $year < $selectedMonth->year;
            })
            ->last();

        if ($previousYear === null) {
            return null;
        }

        return $this->pickClosestMonthForYear($availableMonths, (int) $previousYear, (int) $selectedMonth->month);
    }

    private function findNextAvailableYear(Collection $availableMonths, Carbon $selectedMonth): ?Carbon
    {
        $nextYear = $availableMonths
            ->pluck('year')
            ->unique()
            ->first(function (int $year) use ($selectedMonth) {
                return $year > $selectedMonth->year;
            });

        if ($nextYear === null) {
            return null;
        }

        return $this->pickClosestMonthForYear($availableMonths, (int) $nextYear, (int) $selectedMonth->month);
    }

    private function pickClosestMonthForYear(Collection $availableMonths, int $year, int $preferredMonth): ?Carbon
    {
        $monthsInYear = $availableMonths
            ->filter(function (Carbon $month) use ($year) {
                return $month->year === $year;
            })
            ->sortBy(function (Carbon $month) use ($preferredMonth) {
                return (abs($month->month - $preferredMonth) * 100) + $month->month;
            })
            ->values();

        return $monthsInYear->first();
    }

    private function toNavigationPayload(?Carbon $date): ?array
    {
        if ($date === null) {
            return null;
        }

        return [
            'month' => $date->month,
            'year' => $date->year,
        ];
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




