<?php

namespace App\Helpers;

use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use App\Models\SportEquipment;
use App\Models\SportEquipmentBooked;

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

    public static function sportgeraetanzahlMax($id)
    {
        // Berechnung basierend auf Sportlerplätze - sum('sportleranzahl')
        $sportgeraetanzahlMax = SportEquipment::join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
            ->where('organiser_sport_section.organiser_id' , $id)
            ->sum('sport_equipment.sportleranzahl');

        return $sportgeraetanzahlMax;
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


}
