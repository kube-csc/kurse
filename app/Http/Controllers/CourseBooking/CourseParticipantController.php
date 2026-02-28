<?php

namespace App\Http\Controllers\CourseBooking;

use App\Helpers\CoursedateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCourseParticipantRequest;
use App\Models\Coursedate;
use App\Models\CourseParticipant;
use App\Models\CourseParticipantBooked;
use App\Models\SportEquipment;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
//use PhpParser\Node\Stmt\Return_;

class CourseParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organiser = $this->organiser();

        $coursedates = Coursedate::where('coursedates.organiser_id', $organiser->id)
            // ToDo:Vorher Filter so das nur noch Ergebnisse vorhanden sind die den angemeldeten Kursleiter zugeordnet sind
            // Aktuel wird das in der blade mit einer if Abfrage gemacht
            //->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            //->where('coursedate_user.user_id', Auth::user()->id)
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->withCount(['courseParticipantBookeds as booked_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id');
            }])
            ->withCount(['courseParticipantBookeds as bookedSelf_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id')->where('participant_id', Auth::user()->id);
            }])
            ->orderBy('kursstarttermin')
            //->paginate(20);
            ->get();

        return view('components.courseBooking.course.index', compact('coursedates', 'organiser'));
    }

    public function indexParticipant()
    {
        $organiser = $this->organiser();

        $coursedates = Coursedate::where('coursedates.organiser_id', $organiser->id)
            // ToDo:Vorher Filter so das nur noch Ergebnisse vorhanden sind die den angemeldeten Kursleiter zugeordnet sind
            // Aktuel wird das in der blade mit einer if Abfrage gemacht
            //->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            //->where('coursedate_user.user_id', Auth::user()->id)
            ->join('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->where('participant_id', Auth::user()->id)
            ->whereNull('course_participant_bookeds.deleted_at')
            ->withCount(['courseParticipantBookeds as booked_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id');
            }])
            ->withCount(['courseParticipantBookeds as bookedSelf_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id')->where('participant_id', Auth::user()->id);
            }])
            ->distinct()
            ->orderBy('kursstarttermin')
            ->get();

        return view('components.courseBooking.course.indexParticipant', compact('coursedates', 'organiser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $coursedate = Coursedate::find($id);

        $organiser = $this->organiser();

        $courseBookes = CourseParticipantBooked::where('kurs_id', $id)
            ->where('participant_id' , Auth::user()->id)
            ->get();

        $courseBookedAlls = CourseParticipantBooked::where('kurs_id', $id)
            ->get();

        $courseBookedAlls = $courseBookedAlls->diff($courseBookes);

        $teilnehmerKursBookeds = CourseParticipantBooked::where('kurs_id', '<>' , $id)
            ->join('coursedates', 'coursedates.id', '=', 'course_participant_bookeds.kurs_id')
            ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            ->join('users', 'users.id', '=', 'coursedate_user.user_id')
            ->where('course_participant_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>', $coursedate->kursstarttermin)
            ->get();

        $sportEquipments = Coursedate::join('course_sport_section', 'course_sport_section.course_id', '=', 'coursedates.course_id')
            ->join('sport_equipment', 'sport_equipment.sportSection_id', '=', 'course_sport_section.sport_section_id')
            ->where('coursedates.id', $coursedate->id)
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

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

        // Gebuchte Boote für den Kurs
        $sportEquipmentKursBookeds = SportEquipment::join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
            ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('sport_equipment_bookeds.kurs_id', $coursedate->id)
            ->where('organiser_sport_section.organiser_id' , $this->organiserDomainId())
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

        $bookedIds           = $sportEquipmentBookeds->pluck('sportgeraet_id');
        $kursBbookeIds    = $sportEquipmentKursBookeds->pluck('sportgeraet_id');
        $sportEquipments= $sportEquipments->whereNotIn('id', $bookedIds);
        $sportEquipments= $sportEquipments->whereNotIn('id', $kursBbookeIds);

        $freeSportEquipment =$sportEquipmentBookeds->count()-$teilnehmerKursBookeds->count();
        if($freeSportEquipment>0){
            $freeSportEquipment=0;
        }

        if($coursedate->sportgeraetanzahl==0) {
            $sportgeraetanzahlMax = $sportEquipments->count()+$sportEquipmentKursBookeds->count()-$courseBookes->count()-$courseBookedAlls->count()+$freeSportEquipment;
        }
        else {
            if($sportEquipments->count()+$sportEquipmentKursBookeds->count()>$coursedate->sportgeraetanzahl) {
                $sportgeraetanzahlMax = $coursedate->sportgeraetanzahl-$courseBookes->count()-$courseBookedAlls->count();
            }
            else {
                $sportgeraetanzahlMax = $sportEquipments->count();
            }
        }
        $timeMin=Carbon::parse($coursedate->kursstarttermin)->format('H:i');
        $courseLength = Carbon::parse($coursedate->kurslaenge);
        $courseLengthInMinutes = $courseLength->hour * 60 + $courseLength->minute;
        $timeMax = Carbon::parse($coursedate->kursendtermin)->subMinutes($courseLengthInMinutes)->format('H:i');

        // Neu
        $sportEquipmentBookedsForCoursedatesSum = $sportEquipmentKursBookeds->count();

        $overlapStats = CoursedateHelper::getOverlapBookingStats($coursedate);
        $needEquipmentProCourstimeSumme = $overlapStats->sum('max');

        $sportgeraetanzahlMax = CoursedateHelper::sportgeraetanzahlMax($coursedate->organiser_id);
        $maxReservierbarInput =  $sportgeraetanzahlMax - $needEquipmentProCourstimeSumme;
        $maxParticipant = $sportgeraetanzahlMax  - $needEquipmentProCourstimeSumme;

        if($maxParticipant > $coursedate->sportgeraetanzahl) {
            $maxParticipant = $coursedate->sportgeraetanzahl;
        }

        $maxReservierbarInput = (max ($sportEquipmentBookedsForCoursedatesSum, $maxReservierbarInput))-$courseBookes->count()-$courseBookedAlls->count();;

        return view('components.courseBooking.course.edit', compact([
                'coursedate',
                'sportgeraetanzahlMax',
                'organiser',
                'courseBookes',
                'courseBookedAlls',
                'timeMax',
                'timeMin',
                // neu für Reservierung/Details
                'maxParticipant',
                'maxReservierbarInput',
                'sportEquipmentBookedsForCoursedatesSum',
                'needEquipmentProCourstimeSumme'
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseParticipantRequest $request, Coursedate $coursedate)
    {
        //$coursedate->update($request->validated());
        // ToDo: Valedierung verbessern
        $daten=$this->kursendtermin($request, $coursedate);

        $courseParticipantBookedCount = CourseParticipantBooked::where('kurs_id' , $coursedate->id)->count();
        if($courseParticipantBookedCount>0){
            self::warning('Der Zeit kann nicht bearbeitet werden, da bereits Teilnehmer gebucht sind. Es können aber  weiter Teilnehmer gebucht werden.');
            return redirect()->route('courseBooking.course.edit', $coursedate->id);
        }

        $coursedate->update(
            [
               'kursstarttermin'                 => $daten['kursstarttermin'],
               'kursendtermin'                  => $daten['kursendtermin'],
               'kursstartvorschlagkunde'  => $daten['kursstarttermin'],
               'kursendvorschlagkunde'   => $daten['kursendtermin'],
           ]
        );

        $this->book($coursedate->id);

        $this->testBookCount($coursedate->id);

        self::success('Die Zeit für den Termin wurde angepasst.');

        return redirect()->route('courseBooking.course.edit', $coursedate->id);
    }

    public function book($coursedateId)
    {
        $coursedate = Coursedate::find($coursedateId);
        if (!$coursedate) {
            self::warning('Der Termin wurde nicht gefunden oder ist nicht mehr verfügbar.');
            return redirect()->route('courseBooking.course.index');
        }

        $overlapStats = CoursedateHelper::getOverlapBookingStats($coursedate);
        $needEquipmentProCourstimeSumme = (int) $overlapStats->sum('max');

        $sportgeraetanzahlMax = (int) (CoursedateHelper::sportgeraetanzahlMax($coursedate->organiser_id) ?? 0);
        $maxReservierbarInput = $sportgeraetanzahlMax - $needEquipmentProCourstimeSumme;

        $courseBookedAllCount = (int) CourseParticipantBooked::where('kurs_id', $coursedateId)->count();
        $sportgeraetanzahl = (int) ($coursedate->sportgeraetanzahl ?? 0);

        // Wenn sportgeraetanzahl == 0, interpretieren wir das als "kein fixes Teilnehmerlimit".
        // Dann limitiert nur die Reservierbarkeit (Material/Überlappungen).
        $hasFreeSlotInCoursedate = $sportgeraetanzahl === 0
            ? true
            : ($courseBookedAllCount < $sportgeraetanzahl);

        if ($maxReservierbarInput > 0 && $hasFreeSlotInCoursedate) {
            $participantBook = new CourseParticipantBooked(
                [
                    'participant_id' => Auth::user()->id,
                    'kurs_id' => $coursedateId,
                ]
            );

            $participantBook->save();

            // Holen Sie sich alle Benutzer-IDs, die dem $coursedate zugeordnet sind
            $userIds = DB::table('coursedate_user')->where('coursedate_id', $coursedateId)->pluck('user_id');

            // Kursleiter-Hinweis setzen
            DB::table('users')
                ->whereIn('id', $userIds)
                ->where('trainernachricht', '')
                ->update(['trainernachricht' => 1]);

            // Teilnehmer-Hinweis setzen
            DB::table('course_participants')
                ->where('id', Auth::user()->id)
                ->where('teilnehmernachricht', '')
                ->update(['teilnehmernachricht' => 1]);

            self::success('Ein Teilnehmer wurde erfolgreich gebucht.');
        } else {
            if ($maxReservierbarInput <= 0) {
                self::warning('Für diesen Zeitraum sind keine weiteren Reservierungen möglich (Sportgeräte bereits vollständig verplant).');
            } else {
                self::warning('Die maximale Anzahl an Teilnehmer ist erreicht. Es können keine weiteren Teilnehmer gebucht werden.');
            }
        }

        return redirect()->route('courseBooking.course.edit', $coursedateId);
    }

    public function destroyBooked($coursedateId , $courseBookId)
    {
        $sportEquipmentBooked = CourseParticipantBooked::find($courseBookId);

        $sportEquipmentBooked->delete();

        $courseBookedCount = CourseParticipantBooked::where('kurs_id', $coursedateId)->count();

        if($courseBookedCount == 0)
        {
            $coursedate = Coursedate::find($coursedateId);
            $coursedate->update(
                [
                    'kursstarttermin'         => $coursedate->kursstartvorschlag,
                    'kursendtermin'           => $coursedate->kursendvorschlag,
                    'kursstartvorschlagkunde' => $coursedate->kursstartvorschlag,
                    'kursendvorschlagkunde'   => $coursedate->kursendvorschlag,
                    'kursNichtDurchfuerbar'   => false,
                ]
            );
        }

        self::success('Ein Teilnehmer wurde erfolgreich storniert.');

        return redirect()->route('courseBooking.course.edit', $coursedateId);
    }

    public function bookedCount($coursedate)
    {
        //ToDo: Auf Sportplätze umstellen ->sum('sportleranzahl')
        $courseBookes = CourseParticipantBooked::where('kurs_id', $coursedate->id)->get();

        // Alle Sportgeräte
        $sportEquipments = Coursedate::
        join('course_sport_section', 'course_sport_section.course_id', '=', 'coursedates.course_id')
            ->join('sport_equipment', 'sport_equipment.sportSection_id', '=', 'course_sport_section.sport_section_id')
            ->where('coursedates.id', $coursedate->id)
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

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

        // Gebuchte Boote für den Kurs
        $sportEquipmentKursBookeds = SportEquipment::
        join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
            ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('sport_equipment_bookeds.kurs_id', $coursedate->id)
            ->where('organiser_sport_section.organiser_id' , $coursedate->organiser_id)
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

        $bookedIds           = $sportEquipmentBookeds->pluck('sportgeraet_id');
        $kursBbookeIds       = $sportEquipmentKursBookeds->pluck('sportgeraet_id');
        $sportEquipmentFrees = $sportEquipments->whereNotIn('id', $bookedIds);
        $sportEquipmentFrees = $sportEquipmentFrees->whereNotIn('id', $kursBbookeIds);

        if($coursedate->sportgeraetanzahl==0) {
            $sportgeraetanzahlMax = $sportEquipmentFrees->count()+$sportEquipmentKursBookeds->count();
        }
        else {
            $sportgeraetanzahlMax = $coursedate->sportgeraetanzahl-$courseBookes->count();
            if($sportgeraetanzahlMax>$sportEquipmentFrees->count()+$sportEquipmentKursBookeds->count()) {
                $sportgeraetanzahlMax = $sportEquipmentFrees->count();
            }
            $sportgeraetanzahlMax=$sportgeraetanzahlMax+$courseBookes->count();
        }

        return [
            'sportgeraetanzahlMax' => $sportgeraetanzahlMax,
            'courseBookesCount'    => $courseBookes->count(),
        ];
    }

}
