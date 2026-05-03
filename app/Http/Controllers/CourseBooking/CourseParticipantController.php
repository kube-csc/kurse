<?php

namespace App\Http\Controllers\CourseBooking;

use App\Helpers\CoursedateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCourseParticipantRequest;
use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use App\Models\SportEquipment;
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

        $courseIdsParam = request()->input('course_ids');

        // Wenn im Request nicht vorhanden, versuche aus Session zu laden
        if ($courseIdsParam === null && session()->has('course_embed_filter')) {
            $courseIdsParam = session('course_embed_filter');
        }

        $filterCourseIds = [];
        if ($courseIdsParam !== null) {
            if (is_array($courseIdsParam)) {
                $filterCourseIds = array_map('intval', $courseIdsParam);
            } else {
                $filterCourseIds = array_map('intval', explode(',', $courseIdsParam));
            }
            $filterCourseIds = array_filter($filterCourseIds);
        }

        $coursedatesQuery = Coursedate::where('coursedates.organiser_id', $organiser->id)
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
            }]);

        if (!empty($filterCourseIds)) {
            $coursedatesQuery->whereIn('coursedates.course_id', $filterCourseIds);
        }

        $coursedates = $coursedatesQuery->orderBy('kursstarttermin')->get();

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
     * Öffentliche Einbettungsansicht für externe Webseiten.
     */
    public function embed()
    {
        $organiser = $this->organiser();
        $debugUrl  = request()->fullUrl();
        $showDebug = config('app.debug') && request()->boolean('debug');

        $isCourseParticipantLoggedIn = Auth::check()
            && method_exists(Auth::user(), 'getTable')
            && Auth::user()->getTable() === 'course_participants';

        // Optionaler Filter: ?course_ids=1,2,3  oder  ?course_ids[]=1&course_ids[]=2
        $courseIdsParam = request()->input('course_ids');

        // In Session speichern, falls im Request vorhanden
        if (request()->has('course_ids')) {
            session(['course_embed_filter' => $courseIdsParam]);
        }

        $filterCourseIds = [];
        if ($courseIdsParam !== null) {
            if (is_array($courseIdsParam)) {
                $filterCourseIds = array_map('intval', $courseIdsParam);
            } else {
                $filterCourseIds = array_map('intval', explode(',', $courseIdsParam));
            }
            $filterCourseIds = array_filter($filterCourseIds); // 0-Werte entfernen
        }

        $coursedatesQuery = Coursedate::where('coursedates.organiser_id', $organiser->id)
            ->where('kursstarttermin', '>=', date('Y-m-d', strtotime('now')))
            ->with(['getCousename', 'users'])
            ->withCount(['courseParticipantBookeds as booked_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id');
            }])
            ->orderBy('kursstarttermin');

        // Kursfilterung anwenden (course_id = Kursvorlage, nicht Kurstermin-ID)
        if (!empty($filterCourseIds)) {
            $coursedatesQuery->whereIn('coursedates.course_id', $filterCourseIds);
        }

        if ($isCourseParticipantLoggedIn) {
            $participantId = (int) Auth::id();
            $coursedatesQuery->withCount(['courseParticipantBookeds as bookedSelf_count' => function ($query) use ($participantId) {
                $query->whereColumn('kurs_id', 'coursedates.id')->where('participant_id', $participantId);
            }]);
        }

        $coursedates = $coursedatesQuery->get();

        if (!$isCourseParticipantLoggedIn) {
            $coursedates->each(function ($coursedate) {
                $coursedate->bookedSelf_count = 0;
            });
        }

        if (request()->wantsJson() || request()->has('json')) {
            return response()->json([
                'coursedates' => $coursedates,
                'isLoggedIn' => $isCourseParticipantLoggedIn,
                'organiser' => $organiser
            ]);
        }

        return view('components.embed.course.courseEmbed', compact(
            'coursedates',
            'organiser',
            'isCourseParticipantLoggedIn',
            'debugUrl',
            'filterCourseIds',
            'showDebug'
        ));
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

        // Berechnung mit sum('sportleranzahl') statt count()
        $freeSportEquipmentSum = $sportEquipments->sum('sportleranzahl');
        $kursBookedSum = $sportEquipmentKursBookeds->sum('sportleranzahl');
        $sportEquipmentBookedsSum = $sportEquipmentBookeds->sum('sportleranzahl');
        $teilnehmerKursBookedsSum = $teilnehmerKursBookeds->count();

        $freeSportEquipment = $sportEquipmentBookedsSum - $teilnehmerKursBookedsSum;
        if($freeSportEquipment>0){
            $freeSportEquipment=0;
        }

        if($coursedate->sportgeraetanzahl==0) {
            $sportgeraetanzahlMax = $freeSportEquipmentSum + $kursBookedSum - $courseBookes->count() - $courseBookedAlls->count() + $freeSportEquipment;
        }
        else {
            if($freeSportEquipmentSum + $kursBookedSum > $coursedate->sportgeraetanzahl) {
                $sportgeraetanzahlMax = $coursedate->sportgeraetanzahl - $courseBookes->count() - $courseBookedAlls->count();
            }
            else {
                $sportgeraetanzahlMax = $freeSportEquipmentSum;
            }
        }
        $timeMin=Carbon::parse($coursedate->kursstarttermin)->format('H:i');
        $courseLength = Carbon::parse($coursedate->kurslaenge);
        $courseLengthInMinutes = $courseLength->hour * 60 + $courseLength->minute;
        $timeMax = Carbon::parse($coursedate->kursendtermin)->subMinutes($courseLengthInMinutes)->format('H:i');

        // Neu - mit sum('sportleranzahl')
        $sportEquipmentBookedsForCoursedatesSum = $kursBookedSum;

        $overlapStats = CoursedateHelper::getOverlapBookingStats($coursedate);
        $needEquipmentProCourstimeSumme = $overlapStats->sum('max');

        $sportgeraetanzahlMax = CoursedateHelper::sportgeraetanzahlMaxPlaetze($coursedate->organiser_id);
        $maxReservierbarInput =  $sportgeraetanzahlMax - $needEquipmentProCourstimeSumme;
        $maxParticipant = $sportgeraetanzahlMax  - $needEquipmentProCourstimeSumme;

        if($maxParticipant > $coursedate->sportgeraetanzahl) {
            $maxParticipant = $coursedate->sportgeraetanzahl;
        }

        $maxReservierbarInput = (max ($sportEquipmentBookedsForCoursedatesSum, $maxReservierbarInput))-$courseBookes->count()-$courseBookedAlls->count();

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

        // Alle Sportgeräte
        $sportEquipments= CoursedateHelper::getSportEquipments($coursedate);

        // Belegte Sportgeräte andere Kurse
        $sportEquipmentBookeds = CoursedateHelper::getSportEquipmentBookeds($coursedate);

        // Gebuchte Sportgeräte für den Kurs
        $sportEquipmentKursBookeds = CoursedateHelper::getSportEquipmentKursBookeds($coursedate);

        $bookedIds = $sportEquipmentBookeds->pluck('sportgeraet_id');
        $kursBookeIds = $sportEquipmentKursBookeds->pluck('id');
        $sportEquipmentPool = $sportEquipments->whereNotIn('id', $bookedIds);
        $sportEquipmentPool = $sportEquipmentPool->whereNotIn('id', $kursBookeIds);

        // Allocation berechnen und poolHasRemainingPlace als Gate verwenden
        $overlapingCoursedates = CoursedateHelper::getOverlappingCoursedates($coursedate);
        $overlapingCoursedates->push($coursedate);
        $overlapingCoursedatesWithParticipants = CoursedateHelper::getParticipantCountForOverlappingCoursedates($overlapingCoursedates);

        $allocationResult = CoursedateHelper::allocateFreeSportEquipmentGreedy(
            $overlapingCoursedatesWithParticipants,
            $sportEquipmentPool,
            $coursedate->id
        );

        if (empty($allocationResult['poolHasRemainingPlace'])) {
            self::warning('Es sind keine freien Plätze im Sportgeräte-Pool vorhanden. Der Teilnehmer kann nicht gebucht werden.');
            return redirect()->route('courseBooking.course.edit', $coursedateId);
        }

        $participantBook = new CourseParticipantBooked(
            [
                'participant_id' => Auth::user()->id,
                'kurs_id' => $coursedateId,
            ]
        );

        $participantBook->save();

        // Holen Sie sich alle Benutzer-IDs, die dem $coursedate zugeordnet sind
        $userIds = DB::table('coursedate_user')
            ->where('coursedate_id', $coursedateId)
            ->pluck('user_id');

        // Kursleiter-Hinweis setzen
        DB::table('users')
            ->whereIn('id', $userIds)
            ->where(function($query) {
                $query->where('trainernachricht', '=', '0')
                      ->orWhere('trainernachricht', '=', '');
            })
            ->update(['trainernachricht' => 1]);

        // Teilnehmer-Hinweis setzen
        DB::table('course_participants')
            ->where('id', Auth::user()->id)
            ->where(function($query) {
                $query->where('teilnehmernachricht', '=', '0')
                      ->orWhere('teilnehmernachricht', '=', '');
            })
            ->update(['teilnehmernachricht' => 1]);

        self::success('Ein Teilnehmer wurde erfolgreich gebucht.');

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
        // Berechnung basierend auf Sportlerplätze - sum('sportleranzahl')
        $courseBookes = CourseParticipantBooked::where('kurs_id', $coursedate->id)->get();

        // Alle Sportgeräte - mit Sportleranzahl
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
        $sportEquipmentPool = $sportEquipments->whereNotIn('id', $bookedIds);
        $sportEquipmentPool = $sportEquipmentPool->whereNotIn('id', $kursBbookeIds);

        // Berechnung mit sum('sportleranzahl') statt count()
        $freeSportEquipmentSum = $sportEquipmentPool->sum('sportleranzahl');
        $kursBookedSum = $sportEquipmentKursBookeds->sum('sportleranzahl');

        if($coursedate->sportgeraetanzahl==0) {
            $sportgeraetanzahlMax = $freeSportEquipmentSum + $kursBookedSum;
        }
        else {
            $sportgeraetanzahlMax = $coursedate->sportgeraetanzahl - $courseBookes->count();
            if($sportgeraetanzahlMax > $freeSportEquipmentSum + $kursBookedSum) {
                $sportgeraetanzahlMax = $freeSportEquipmentSum;
            }
            $sportgeraetanzahlMax = $sportgeraetanzahlMax + $courseBookes->count();
        }

        return [
            'sportgeraetanzahlMax' => $sportgeraetanzahlMax,
            'courseBookesCount'    => $courseBookes->count(),
        ];
    }

}
