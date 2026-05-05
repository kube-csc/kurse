<?php

namespace App\Helpers;

use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use App\Models\SportEquipment;
use App\Models\SportEquipmentBooked;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CoursedateHelper
{
    /**
     * Gibt alle Coursedates zurück, die sich mit dem gegebenen $coursedate überschneiden (außer sich selbst).
     * Berücksichtigt Einzeltermine bei mehrtägigen Kursen.
     */
    public static function getOverlappingCoursedates($coursedate)
    {
        $overlapping = collect();
        $otherCoursedates = Coursedate::where('id', '<>', $coursedate->id)
            ->where('kursstarttermin', '<', $coursedate->kursendtermin)
            ->where('kursendtermin', '>', $coursedate->kursstarttermin)
            ->with('course')
            ->get();

        $thisStart = \Carbon\Carbon::parse($coursedate->kursstarttermin);
        $thisEnd   = \Carbon\Carbon::parse($coursedate->kursendtermin);
        foreach ($otherCoursedates as $other) {
            $otherStart = \Carbon\Carbon::parse($other->kursstarttermin);
            $otherEnd = \Carbon\Carbon::parse($other->kursendtermin);
            $kurslaenge = $other->kurslaenge ?? '01:00';
            $kurslaengeParts = explode(':', $kurslaenge);
            $kurslaengeMin = ((int)($kurslaengeParts[0] ?? 1)) * 60 + ((int)($kurslaengeParts[1] ?? 0));

            $current = $otherStart->copy();
            while ($current->lte($otherEnd)) {
                $einzelStart = $current->copy();
                $einzelEnd = $current->copy()->addMinutes($kurslaengeMin);

                if ($einzelStart->lt($thisEnd) && $einzelEnd->gt($thisStart)) {
                    $overlapping->push($other);
                    break;
                }
                $current->addDay();
            }
        }
        return $overlapping;
    }

    /**
     * Gibt alle Teilnehmerbuchungen für Coursedates zurück, die sich mit dem gegebenen $coursedate überschneiden.
     */
    public static function getTeilnehmerKursBookedsForCoursedates($coursedate)
    {
        // Nutze die bestehende Funktion für Überschneidungen
        $overlappingCoursedates = self::getOverlappingCoursedates($coursedate);

        $ids = $overlappingCoursedates->pluck('id');
        if ($ids->isEmpty()) {
            return collect();
        }
        return CourseParticipantBooked::whereIn('kurs_id', $ids)
            ->whereNull('course_participant_bookeds.deleted_at')
            ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'course_participant_bookeds.kurs_id')
            ->join('users', 'users.id', '=', 'coursedate_user.user_id')
            ->get(['users.vorname', 'users.nachname']);
    }

    /**
     * Gibt alle belegten Sportgeräte für eine Liste von Coursedates zurück.
     * Berücksichtigt Einzeltermine bei mehrtägigen Kursen.
     */
    public static function getSportEquipmentBookedsForCoursedates($coursedate, $excludeId = null)
    {
        $sportEquipmentBookeds = collect();
        $overlappingCoursedates = self::getOverlappingCoursedates($coursedate);

        foreach ($overlappingCoursedates as $other) {
            $query = SportEquipment::join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
                ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
                ->leftJoin('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
                ->leftJoin('users', 'users.id', '=', 'coursedate_user.user_id')
                ->whereNull('sport_equipment_bookeds.deleted_at')
                ->where('sport_equipment_bookeds.kurs_id', $other->id)
                ->orderBy('sport_equipment.sportgeraet');

            if ($excludeId !== null) {
                $query->where('sport_equipment_bookeds.kurs_id', '<>', $excludeId);
            }

            $bookeds = $query->get();
            $sportEquipmentBookeds = $sportEquipmentBookeds->merge($bookeds);
        }

        return $sportEquipmentBookeds;
    }

    /**
     * Erstellt pro überlappendem Coursedate einen Datensatz mit:
     * - TeilnehmerKursBookeds (Count)
     * - SportEquipmentBookeds (Count)
     * - sportgeraeteReserviert (Wert aus dem überlappenden Coursedate)
     * Zusätzlich werden min/max über diese drei Werte angehängt.
     *
     * Rückgabe: Collection von Arrays.
     */
    public static function getOverlapBookingStats($coursedate)
    {
        $overlappingCoursedates = self::getOverlappingCoursedates($coursedate);

        if ($overlappingCoursedates->isEmpty()) {
            return collect();
        }

        $ids = $overlappingCoursedates->pluck('id')->values();

        // Sammelqueries, um N+1 zu vermeiden
        $teilnehmerCounts = CourseParticipantBooked::query()
            ->whereIn('kurs_id', $ids)
            ->whereNull('deleted_at')
            ->selectRaw('kurs_id, COUNT(*) as cnt')
            ->groupBy('kurs_id')
            ->pluck('cnt', 'kurs_id');

        $sportEquipmentCounts = SportEquipmentBooked::query()
            ->whereIn('kurs_id', $ids)
            ->whereNull('deleted_at')
            ->selectRaw('kurs_id, COUNT(*) as cnt')
            ->groupBy('kurs_id')
            ->pluck('cnt', 'kurs_id');

        return $overlappingCoursedates->map(function ($overlap) use ($teilnehmerCounts, $sportEquipmentCounts) {
            $teilnehmer = (int) ($teilnehmerCounts[$overlap->id] ?? 0);
            $sportEquipment = (int) ($sportEquipmentCounts[$overlap->id] ?? 0);
            $reserviert = (int) ($overlap->sportgeraeteReserviert ?? 0);

            $min = min($teilnehmer, $sportEquipment, $reserviert);
            $max = max($teilnehmer, $sportEquipment, $reserviert);

            return [
                'coursedate' => $overlap,
                'coursedate_id' => $overlap->id,
                'teilnehmerKursBookeds' => $teilnehmer,
                'sportEquipmentBookeds' => $sportEquipment,
                'sportgeraeteReserviert' => $reserviert,
                'min' => $min,
                'max' => $max,
            ];
        })->values();
    }

    public static function sportgeraetanzahlMaxPlaetze($id)
    {
        // Berechnung basierend auf Sportlerplätze - sum('sportleranzahl')
        $sportgeraetanzahlMaxPlaetze = SportEquipment::join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
            ->where('organiser_sport_section.organiser_id' , $id)
            ->sum('sport_equipment.sportleranzahl');

        return $sportgeraetanzahlMaxPlaetze;
    }

    public static function getSportEquipments($coursedate)
    {
        $sportEquipments = Coursedate::join('course_sport_section', 'course_sport_section.course_id', '=', 'coursedates.course_id')
            ->join('sport_equipment', 'sport_equipment.sportSection_id', '=', 'course_sport_section.sport_section_id')
            ->where('coursedates.id', $coursedate->id)
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

        return $sportEquipments;
    }

    public static function getSportEquipmentBookeds($coursedate)
    {
        // Belegte Boote andere Kurse
        $sportEquipmentBookeds = SportEquipment::join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->leftJoin('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            ->leftJoin('users', 'users.id', '=', 'coursedate_user.user_id')
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>', $coursedate->kursstarttermin)
            ->whereNot('sport_equipment_bookeds.kurs_id', $coursedate->id)
            ->orderBy('sport_equipment.sportgeraet')
            ->selectRaw("sport_equipment.*, sport_equipment_bookeds.sportgeraet_id, sport_equipment_bookeds.kurs_id, COALESCE(users.vorname, 'ohne Trainer') as vorname, COALESCE(users.nachname, '') as nachname")
            ->get();

        return $sportEquipmentBookeds;
    }


    /**
     * Gibt alle CourseParticipantBooked-Datensätze zurück für Coursedates, die sich mit dem gegebenen $coursedate überschneiden
     * (außer dem gleichen Coursedate).
     */
    public static function getTeilnehmerKursBookedsForOtherCoursedates($coursedate)
    {
        return CourseParticipantBooked::where('kurs_id', '<>', $coursedate->id)
            ->join('coursedates', 'coursedates.id', '=', 'course_participant_bookeds.kurs_id')
            ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            ->join('users', 'users.id', '=', 'coursedate_user.user_id')
            ->where('course_participant_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>', $coursedate->kursstarttermin)
            ->get();
    }

    /**
     * Gibt die Teilnehmerzahlen für jeden überlappenden Coursedate zurück.
     * Rückgabe: Collection mit Bedarf je Coursedate.
     */
    public static function getParticipantCountForOverlappingCoursedates($coursedates)
    {
        if ($coursedates->isEmpty()) {
            return collect();
        }

        $ids = $coursedates->pluck('id')->values();

        $teilnehmerCounts = CourseParticipantBooked::query()
            ->whereIn('kurs_id', $ids)
            ->whereNull('deleted_at')
            ->selectRaw('kurs_id, COUNT(*) as cnt')
            ->groupBy('kurs_id')
            ->pluck('cnt', 'kurs_id');

        $sportgeraeteTeilnehmerplaetze = SportEquipmentBooked::query()
            ->join('sport_equipment', 'sport_equipment.id', '=', 'sport_equipment_bookeds.sportgeraet_id')
            ->whereIn('sport_equipment_bookeds.kurs_id', $ids)
            ->whereNull('sport_equipment_bookeds.deleted_at')
            ->selectRaw('sport_equipment_bookeds.kurs_id, COALESCE(SUM(sport_equipment.sportleranzahl), 0) as plaetze')
            ->groupBy('sport_equipment_bookeds.kurs_id')
            ->pluck('plaetze', 'kurs_id');

        $gebuchteSportgeraeteCounts = SportEquipmentBooked::query()
            ->whereIn('kurs_id', $ids)
            ->whereNull('deleted_at')
            ->selectRaw('kurs_id, COUNT(*) as cnt')
            ->groupBy('kurs_id')
            ->pluck('cnt', 'kurs_id');

        return $coursedates->map(function ($coursedate) use ($teilnehmerCounts, $sportgeraeteTeilnehmerplaetze, $gebuchteSportgeraeteCounts) {
            $teilnehmerCount = (int) ($teilnehmerCounts[$coursedate->id] ?? 0);
            $sportgeraeteReserviert = (int) ($coursedate->sportgeraeteReserviert ?? 0);
            $gebuchteSportgeraetePlaetze = (int) ($sportgeraeteTeilnehmerplaetze[$coursedate->id] ?? 0);
            $gebuchteSportgeraeteAnzahl = (int) ($gebuchteSportgeraeteCounts[$coursedate->id] ?? 0);
            $maxTeilnehmer = max($teilnehmerCount, $sportgeraeteReserviert);
            $benoetigtePlaetze = max($maxTeilnehmer - $gebuchteSportgeraetePlaetze, 0);

            $sportgeraetanzahl = (int) ($coursedate->sportgeraetanzahl ?? 0);
            if ($sportgeraetanzahl == 0) {
                $benoetigtePlaetzeMax = $benoetigtePlaetze;
            } else {
                $benoetigtePlaetzeMax = min($benoetigtePlaetze, $sportgeraetanzahl);
            }

            return [
                'coursedate' => $coursedate,
                'coursedate_id' => $coursedate->id,
                'course_id' => $coursedate->course_id,
                'teilnehmerCount' => $teilnehmerCount,
                'sportgeraeteReserviert' => $sportgeraeteReserviert,
                'maxTeilnehmer' => $maxTeilnehmer,
                'teilnehmerplaetzeGebuchteSportgeraete' => $gebuchteSportgeraetePlaetze,
                'gebuchteSportgeraeteAnzahl' => $gebuchteSportgeraeteAnzahl,
                'benoetigtePlaetze' => $benoetigtePlaetze,
                'benoetigtePlaetzeMax' => $benoetigtePlaetzeMax,
            ];
        })->values();
    }

    /**
     * Weist freie Sportgeräte pro Coursedate greedy zu (größte Plätze zuerst).
     * Der Pool wird global über alle überlappenden Termine verbraucht,
     * sodass ein Sportgerät nur einmal vergeben werden kann.
     * Rückgabe: items + Pool-Information, ob noch Plätze übrig sind.
     */
    public static function allocateFreeSportEquipmentGreedy($overlapsWithParticipants, $freeSportEquipments, $currentCoursedateId)
    {
        $pool = $freeSportEquipments
            ->sort(function ($a, $b) {
                $plaetzeA = (int) ($a->sportleranzahl ?? 0);
                $plaetzeB = (int) ($b->sportleranzahl ?? 0);

                if ($plaetzeA === $plaetzeB) {
                    return (int) ($a->id ?? 0) <=> (int) ($b->id ?? 0);
                }

                return $plaetzeB <=> $plaetzeA;
            })
            ->values()
            ->map(function ($equipment) {
                return [
                    'id' => $equipment->id,
                    'sportgeraet' => $equipment->sportgeraet ?? null,
                    'plaetze' => (int) ($equipment->sportleranzahl ?? 0),
                ];
            })
            ->all();

        $state = [];
        foreach ($overlapsWithParticipants as $row) {
            $coursedateId = (int) ($row['coursedate_id'] ?? 0);
            $state[$coursedateId] = [
                'row' => $row,
                'restbedarf' => max(0, (int) ($row['benoetigtePlaetzeMax'] ?? 0)),
                'neuZugewiesenePlaetze' => 0,
                'neuZugewieseneSportgeraete' => [],
            ];
        }

        $poolIndex = 0;
        while (isset($pool[$poolIndex])) {
            $targetCoursedateId = null;

            foreach ($state as $coursedateId => $entry) {
                $restbedarf = (int) ($entry['restbedarf'] ?? 0);
                if ($restbedarf <= 0) {
                    continue;
                }

                if ($targetCoursedateId === null) {
                    $targetCoursedateId = $coursedateId;
                    continue;
                }

                $targetRestbedarf = (int) ($state[$targetCoursedateId]['restbedarf'] ?? 0);
                if ($restbedarf > $targetRestbedarf) {
                    $targetCoursedateId = $coursedateId;
                    continue;
                }

                if ($restbedarf === $targetRestbedarf && $coursedateId < $targetCoursedateId) {
                    $targetCoursedateId = $coursedateId;
                }
            }

            // Kein offener Bedarf mehr vorhanden.
            if ($targetCoursedateId === null) {
                break;
            }

            $geraet = $pool[$poolIndex];
            $poolIndex++;

            $state[$targetCoursedateId]['neuZugewieseneSportgeraete'][] = $geraet;
            $state[$targetCoursedateId]['neuZugewiesenePlaetze'] += (int) ($geraet['plaetze'] ?? 0);
            $state[$targetCoursedateId]['restbedarf'] = max(
                0,
                (int) $state[$targetCoursedateId]['restbedarf'] - (int) ($geraet['plaetze'] ?? 0)
            );
        }

        $allocations = [];
        foreach ($state as $coursedateId => $entry) {
            $row = $entry['row'];
            $bereitsGebuchtePlaetze = (int) ($row['teilnehmerplaetzeGebuchteSportgeraete'] ?? 0);
            $bereitsGebuchteAnzahl = (int) ($row['gebuchteSportgeraeteAnzahl'] ?? 0);

            $allocations[$coursedateId] = [
                'zugewieseneSportgeraeteAnzahl' => $bereitsGebuchteAnzahl + count($entry['neuZugewieseneSportgeraete']),
                'zugewiesenePlaetze' => $bereitsGebuchtePlaetze + (int) ($entry['neuZugewiesenePlaetze'] ?? 0),
                'fehlendePlaetze' => max(0, (int) ($entry['restbedarf'] ?? 0)),
                'hatAllePlaetze' => max(0, (int) ($entry['restbedarf'] ?? 0)) === 0,
                'zugewieseneSportgeraete' => $entry['neuZugewieseneSportgeraete'],
            ];
        }

        $items = $overlapsWithParticipants->map(function ($row) use ($allocations) {
            $coursedateId = (int) $row['coursedate_id'];

            if (isset($allocations[$coursedateId])) {
                return array_merge($row, $allocations[$coursedateId]);
            }

            return array_merge($row, [
                'zugewieseneSportgeraeteAnzahl' => (int) ($row['gebuchteSportgeraeteAnzahl'] ?? 0),
                'zugewiesenePlaetze' => (int) ($row['teilnehmerplaetzeGebuchteSportgeraete'] ?? 0),
                'fehlendePlaetze' => (int) ($row['benoetigtePlaetzeMax'] ?? 0),
                'hatAllePlaetze' => ((int) ($row['benoetigtePlaetzeMax'] ?? 0)) === 0,
                'zugewieseneSportgeraete' => [],
            ]);
        });

        $poolRest = array_slice($pool, $poolIndex);
        $poolRemainingPlaetze = array_sum(array_map(function ($geraet) {
            return (int) ($geraet['plaetze'] ?? 0);
        }, $poolRest));

        return [
            'items' => $items,
            'poolRemainingPlaetze' => $poolRemainingPlaetze,
            'poolHasRemainingPlace' => $poolRemainingPlaetze > 0,
            'poolRemainingSportgeraete' => count($poolRest),
        ];
    }

    public static function allocationHasNoMissingPlaces($allocationResult): bool
    {
        $items = [];

        if (is_array($allocationResult)) {
            $items = $allocationResult['items'] ?? [];
        } elseif ($allocationResult instanceof Collection) {
            $items = $allocationResult;
        }

        $items = $items instanceof Collection ? $items : collect($items);

        if ($items->isEmpty()) {
            return false;
        }

        return $items->every(function ($item) {
            return is_array($item)
                && array_key_exists('fehlendePlaetze', $item)
                && (int) $item['fehlendePlaetze'] === 0;
        });
    }

    /**
     * Gibt bereits gebuchte Sportgeräte für einen Coursedate zurück.
     * Berücksichtigt alle überlappenden Termine.
     */
    public static function getSportEquipmentKursBookeds($coursedate)
    {
        // Gebuchte Boote für den Kurs
        $sportEquipmentKursBookeds = SportEquipment::join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
            ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('sport_equipment_bookeds.kurs_id', $coursedate->id)
            ->where('organiser_sport_section.organiser_id' , $coursedate->organiser_id)
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

        return $sportEquipmentKursBookeds;
    }

    /**
     * Ermittelt, wie viele Teilnehmer nur im gegebenen Coursedate eingetragen sind.
     **/
    public static function getTeilnehmerFuerCoursedateCount($coursedate)
    {
        $currentBookings = CourseParticipantBooked::query()
            ->where('kurs_id', $coursedate->id)
            ->whereNull('deleted_at')
            ->get(['participant_id']);

        return $currentBookings;
    }
}
