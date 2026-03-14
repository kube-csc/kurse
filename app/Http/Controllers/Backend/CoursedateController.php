<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Coursedate;
use App\Http\Requests\StoreCoursedateRequest;
use App\Http\Requests\UpdateCoursedateRequest;
use App\Models\CourseParticipantBooked;
use App\Models\SportEquipment;
use App\Models\SportEquipmentBooked;
use App\Models\Trainertable;
use App\Models\Training;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\CoursedateHelper;

class CoursedateController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organiser = $this->organiser();

        $coursedates = Coursedate::where('coursedates.organiser_id', $organiser->id)
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            // ToDo:Vorher Filter das nur noch Ergebnisse vorhanden sind die den angemeldeten Kursleiter zugeordnet sind
            // Aktuell wird das in der blade mit einer if Abfrage gemacht
            //->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            //->where('coursedate_user.user_id', Auth::user()->id)
            ->withCount(['courseParticipantBookeds as booked_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id');
            }])
            ->orderBy('kursstarttermin')
            ->get();

        return view('components.backend.courseDate.index', compact('coursedates', 'organiser'));
    }

    public function indexAll()
    {
        $organiser = $this->organiser();

        $coursedates = Coursedate::where('coursedates.organiser_id', $organiser->id)
            ->join('courses', 'courses.id', '=', 'coursedates.course_id')
            ->select('coursedates.*', 'courses.nicht_anmeldebar as kurs_nicht_anmeldebar')
            ->leftJoin('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            //->whereNull('course_participant_bookeds.deleted_at')
            ->withCount(['courseParticipantBookeds as booked_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id');
            }])
            ->distinct()
            ->orderBy('kursstarttermin')
            ->get();

        return view('components.backend.courseDate.indexAll', compact('coursedates', 'organiser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organiser = $this->organiser();

        $kursstartterminDatum = Carbon::now()->format('Y-m-d');
        $kursstartterminTime = Carbon::now()->format('H:i');
        $kurslaengeStunde = '01';
        $kurslaengeMinute = '30';
        $kurslaenge = $kurslaengeStunde.':'.$kurslaengeMinute;
        $kursendterminDatum=$kursstartterminDatum;
        $kursendterminTime = Carbon::now()->addHours($kurslaengeStunde)->addMinutes($kurslaengeMinute)->format('H:i');

        $trainer = Trainertable::where('user_id', Auth::user()->id)
                               ->where('organiser_id', $organiser->id)
                               ->get();

        if($trainer->count()>0){
        $courses = Course::where('organiser_id', $organiser->id)
                         ->orderBy('kursName')
                         ->get();
        }
        else{
        $courses = Course::where('organiser_id', $organiser->id)
                         ->where('trainer', 0)
                         ->orderBy('kursName')
                         ->get();
        }

        if($courses->count()==0){
            self::warning('Es kann kein Kein Kurs / Fahrt angelegt werden, weil es hierfür keine Vorlage angelegt wurde.');

            return redirect()->back();
        }

        $course_id = 0;
        $sportgeraetanzahl = 0;
        $sportgeraetanzahlMax = CoursedateHelper::sportgeraetanzahlMaxPlaetze($organiser->id);

        return view('components.backend.courseDate.create' , compact([
            'kursstartterminDatum',
            'kursstartterminTime',
            'kurslaenge',
            'kursendterminDatum',
            'kursendterminTime',
            'sportgeraetanzahlMax',
            'sportgeraetanzahl',
            'courses',
            'course_id',
            'organiser'
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCoursedateRequest $request)
    {
        //$data = $request->validated();
        // ToDo: Validierung anpassen

        $message=[
            '1' => 'Der Termin wurde erfolgreich angelegt.',
            '2' => 'Der Endtermin wurde neu berechnet und der Termin wurde erfolgreich angelegt.'
        ];
        $daten=$this->kursendterminMin($request, $message);
        self::success('Der Endtermin wurde neu berechnet und der Termin wurde erfolgreich angelegt.');

        $coursedate = new coursedate(
            [
                'course_id'               => $request->course_id,
                'organiser_id'            => $this->organiserDomainId(),
                'kurslaenge'              => $request->kurslaenge,
                'kursstarttermin'         => $daten['kursstarttermin'],
                'kursendtermin'           => $daten['kursendtermin'],
                'kursstartvorschlag'      => $daten['kursstarttermin'],
                'kursendvorschlag'        => $daten['kursendtermin'],
                'kursstartvorschlagkunde' => $daten['kursstarttermin'],
                'kursendvorschlagkunde'   => $daten['kursendtermin'],
                'sportgeraetanzahl'       => $request->sportgeraetanzahl,
                'kursInformation'         => $request->kursInformation,
                'bearbeiter_id'           => Auth::user()->id,
                'autor_id'                => Auth::user()->id,
            ]
        );
        $coursedate->save();
        $coursedate->users()->attach(Auth::user()->id);

        return redirect()->route('backend.courseDate.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coursedate $coursedate)
    {
     //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $coursedate = Coursedate::find($id);

        $organiser = $this->organiser();

        $courses = Course::where('organiser_id' , $organiser->id)
            ->orderBy('kursName')
            ->get();

        $sportgeraetanzahlMax = CoursedateHelper::sportgeraetanzahlMaxPlaetze($coursedate->organiser_id);

        $overlapRequiredBoatsSum=CoursedateHelper::getSportEquipmentBookedsForCoursedates($coursedate)->count();

        $sportEquipmentBookedsForCoursedatesSum = CoursedateHelper::getSportEquipmentBookedsForCoursedates($coursedate)->count();

        // Debug: neue Overlap-Stats (Counts + min/max)
        $overlapStats = CoursedateHelper::getOverlapBookingStats($coursedate);


        // Variante A: als Array (gut lesbar)
        /*
        dump($overlapStats->map(function ($row) {
            return [
                'coursedate_id' => $row['coursedate_id'],
                'teilnehmerKursBookeds' => $row['teilnehmerKursBookeds'],
                'sportEquipmentBookeds' => $row['sportEquipmentBookeds'],
                'sportgeraeteReserviert' => $row['sportgeraeteReserviert'],
                'min' => $row['min'],
                'max' => $row['max'],
            ];
        })->all());
        */

        // Summe aller "max"-Werte über alle überlappenden Datensätze
        $needEquipmentProCourstimeSumme = $overlapStats->sum('max');

        $maxReservierbarInput = min(
                                                        $coursedate->sportgeraetanzahl,
                                                        $sportgeraetanzahlMax - $needEquipmentProCourstimeSumme
                                                    );
        $maxParticipant = $sportgeraetanzahlMax  - $needEquipmentProCourstimeSumme;

        return view('components.backend.courseDate.edit', compact([
            'coursedate',
            'sportgeraetanzahlMax',
            'maxParticipant',
            'courses',
            'organiser',
            'maxReservierbarInput',
            'overlapRequiredBoatsSum',
            'sportEquipmentBookedsForCoursedatesSum',
             'needEquipmentProCourstimeSumme'
        ]));
    }

    public function editBooked($id)
    {
        $coursedate = Coursedate::find($id);

        $organiser = $this->organiser();

        $courses = Course::where('organiser_id' , $organiser->id)
            ->orderBy('kursName')
            ->get();

        $courseParticipantBookedCount = CourseParticipantBooked::where('kurs_id' , $coursedate->id)->count();

        $sportgeraetanzahlMax = $this->sportgeraetanzahlMax($coursedate->organiser_id);

        return view('components.backend.courseDate.editBooked', compact([
            'coursedate',
            'sportgeraetanzahlMax',
            'courses',
            'organiser',
            'courseParticipantBookedCount'
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCoursedateRequest $request, Coursedate $coursedate)
    {
        //$coursedate->update($request->validated());
        // ToDo: Validierung anpassen

        $message=[
            '1' => 'Der Termin wurde erfolgreich bearbeitet.',
            '2' => 'Der Endtermin wurde neu berechnet und der Termin erfolgreich gespeichert.'
        ];

        $daten=$this->kursendterminMin($request, $message);

        $sportgeraeteReserviert       = (int) ($request->sportgeraeteReserviert ?? 0);
        $sportgeraetanzahl               = (int) ($request->sportgeraetanzahl ?? 0);
        $sportgeraeteReserviertMin = min($sportgeraetanzahl, $sportgeraeteReserviert);

        $coursedate->update(
            [
                'course_id'                        => $request->course_id,
                'kurslaenge'                      => $request->kurslaenge,
                'kursstarttermin'               => $daten['kursstarttermin'],
                'kursendtermin'                => $daten['kursendtermin'],
                'kursstartvorschlag'          => $daten['kursstarttermin'],
                'kursendvorschlag'           => $daten['kursendtermin'],
                'kursstartvorschlagkunde'=> $daten['kursstarttermin'],
                'kursendvorschlagkunde' => $daten['kursendtermin'],
                'sportgeraetanzahl'          => $request->sportgeraetanzahl,
                'kursInformation'              => $request->kursInformation,
                'sportgeraeteReserviert'  => $sportgeraeteReserviertMin,
                'bearbeiter_id'                  => Auth::user()->id,
            ]
        );

        self::success($daten['message']);

        return redirect()->route('backend.courseDate.index');
    }

    public function updateBooked(UpdateCoursedateRequest $request, Coursedate $coursedate)
    {
        // ToDo: Validierung anpassen
        self::success('Der Termin wurde erfolgreich bearbeitet.');

        $coursedate->update(
            [
                'sportgeraetanzahl'       => $request->sportgeraetanzahl,
                'kursInformation'           => $request->kursInformation,
                'bearbeiter_id'               => Auth::user()->id,
            ]
        );

        return redirect()->route('backend.courseDate.index');
    }

    public function updateBookFirst(UpdateCoursedateRequest $request, Coursedate $coursedate)
    {
        $daten=$this->kursendtermin($request, $coursedate);

        $courseParticipantBookedCount = CourseParticipantBooked::where('kurs_id' , $coursedate->id)->count();
        if($courseParticipantBookedCount>0){
            self::warning('Der Termin kann nicht bearbeitet werden, da bereits Teilnehmer gebucht sind. Du kannst aber weiter Teilnehmer hinzufügen.');
            return redirect()->route('backend.courseDate.sportingEquipment', $coursedate->id);
        }

        $coursedate->update(
            [
                'kursstarttermin'        => $daten['kursstarttermin'],
                'kursendtermin'         => $daten['kursendtermin'],
                'bearbeiter_id'           => Auth::user()->id,
            ]
        );

        $this->book($coursedate->id);

        $this->testBookCount($coursedate->id);

        self::success('Die Startzeit wurde im Termin erfolgreich bearbeitet.');

        return redirect()->route('backend.courseDate.sportingEquipment', $coursedate->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coursedate $coursedate)
    {
        $coursedate->delete();
        $coursedate->users()->detach(Auth::user()->id);

        self::success('Kurstermin wurde erfolgreich gelöscht.');

        return redirect()->route('backend.courseDate.index');
    }

    public function sportingEquipment($id)
    {
        $organiser = $this->organiser();

        $coursedate = Coursedate::find($id);

        $course = Course::find($coursedate->course_id);

        $trainers = User::Join('coursedate_user', 'coursedate_user.user_id', '=', 'users.id')
              ->where('coursedate_user.coursedate_id', $coursedate->id)
              ->get();

        $courseBookes = CourseParticipantBooked::where('kurs_id', $id)->get();

        $teilnehmerKursBookeds = CoursedateHelper::getTeilnehmerKursBookedsForOtherCoursedates($coursedate);

        // Alle Sportgeräte
        $sportEquipments= CoursedateHelper::getSportEquipments($coursedate);

        // Belegte Sportgeräte andere Kurse
        $sportEquipmentBookeds = CoursedateHelper::getSportEquipmentBookeds($coursedate);

        // Gebuchte Sportgeräte für den Kurs
        $sportEquipmentKursBookeds = CoursedateHelper::getSportEquipmentKursBookeds($coursedate);

        $bookedIds                   = $sportEquipmentBookeds->pluck('sportgeraet_id');
        $kursBookeIds              = $sportEquipmentKursBookeds->pluck('sportgeraet_id');
        $sportEquipmentPool = $sportEquipments->whereNotIn('id', $bookedIds);
        $sportEquipmentPool = $sportEquipmentPool->whereNotIn('id', $kursBookeIds);

        $overlapingCoursedates = CoursedateHelper::getOverlappingCoursedates($coursedate);
        $overlapingCoursedates->push($coursedate);

        // Bedarf je überlappendem Coursedate berechnen (unsortiert, nur für Berechnung)
        $overlapingCoursedatesWithParticipants = CoursedateHelper::getParticipantCountForOverlappingCoursedates($overlapingCoursedates);

        // Freie Sportgeräte aus dem Pool (größte Plätze zuerst) auf den Bedarf verteilen
        // Nur der aktuelle Termin ($coursedate->id) bekommt Zuweisungen
        $allocationResult = CoursedateHelper::allocateFreeSportEquipmentGreedy(
            $overlapingCoursedatesWithParticipants,
            $sportEquipmentPool,
            $coursedate->id
        );

        // Sortierung nur für die Ausgabe: Werte bleiben per ID fest am richtigen Coursedate
        $rowsByCoursedateId = collect($allocationResult['items'])->keyBy('coursedate_id');
        $sortedForOutput = $overlapingCoursedates
            ->sortBy(function ($cd) {
                return $cd->kursstartvorschlag ?? $cd->kursstarttermin;
            })
            ->values();

        $overlapingCoursedatesWithParticipants = $sortedForOutput
            ->map(function ($cd) use ($rowsByCoursedateId) {
                return $rowsByCoursedateId[$cd->id] ?? [
                    'coursedate' => $cd,
                    'coursedate_id' => $cd->id,
                    'course_id' => $cd->course_id,
                    'teilnehmerCount' => 0,
                    'sportgeraeteReserviert' => 0,
                    'maxTeilnehmer' => 0,
                    'teilnehmerplaetzeGebuchteSportgeraete' => 0,
                    'gebuchteSportgeraeteAnzahl' => 0,
                    'benoetigtePlaetze' => 0,
                    'benoetigtePlaetzeMax' => 0,
                    'zugewieseneSportgeraeteAnzahl' => 0,
                    'zugewiesenePlaetze' => 0,
                    'fehlendePlaetze' => 0,
                    'hatAllePlaetze' => true,
                    'zugewieseneSportgeraete' => [],
                ];
            })
            ->values();

        $poolHasRemainingPlace = $allocationResult['poolHasRemainingPlace'];
        $poolRemainingPlaetze = $allocationResult['poolRemainingPlaetze'];
        $poolRemainingSportgeraete = $allocationResult['poolRemainingSportgeraete'];

        // Berechnung mit sum('sportleranzahl') statt count()
        $freeSportEquipmentSum = $sportEquipmentPool->sum('sportleranzahl');
        $kursBookedSum = $sportEquipmentKursBookeds->sum('sportleranzahl');

        if($coursedate->sportgeraetanzahl==0) {
            $sportgeraetanzahlMax = $freeSportEquipmentSum + $kursBookedSum - $courseBookes->count();
        }
        else {
            $sportgeraetanzahlMax = $coursedate->sportgeraetanzahl - $courseBookes->count();
            if($sportgeraetanzahlMax > $freeSportEquipmentSum + $kursBookedSum) {
                $sportgeraetanzahlMax = $freeSportEquipmentSum;
            }
        }

        $timeMin=Carbon::parse($coursedate->kursstarttermin)->format('H:i');
        $courseLength = Carbon::parse($coursedate->kurslaenge);
        $courseLengthInMinutes = $courseLength->hour * 60 + $courseLength->minute;
        $timeMax = Carbon::parse($coursedate->kursendtermin)->subMinutes($courseLengthInMinutes)->format('H:i');

        return view('components.backend.courseDate.sportingEquipment', compact([
            'organiser',
            'coursedate',
            'course',
            'sportEquipmentPool',
            'freeSportEquipmentSum',
            'sportEquipmentKursBookeds',
            'sportEquipmentBookeds',
            'courseBookes',
            'kursBookedSum',
            'teilnehmerKursBookeds',
            'sportgeraetanzahlMax',
            'trainers',
            'timeMax',
            'timeMin',
            'overlapingCoursedates',
            'overlapingCoursedatesWithParticipants',
            'poolHasRemainingPlace',
            'poolRemainingPlaetze',
            'poolRemainingSportgeraete'
        ]));
    }

    public function book($coursedateId)
    {
        $coursedate = Coursedate::find($coursedateId);

        if (!$coursedate) {
            self::warning('Termin wurde nicht gefunden.');
            return redirect()->route('backend.courseDate.index');
        }

        // Alle Sportgeräte
        $sportEquipments= CoursedateHelper::getSportEquipments($coursedate);

        // Belegte Sportgeräte andere Kurse
        $sportEquipmentBookeds = CoursedateHelper::getSportEquipmentBookeds($coursedate);

        // Gebuchte Sportgeräte für den Kurs
        $sportEquipmentKursBookeds = CoursedateHelper::getSportEquipmentKursBookeds($coursedate);

        $bookedIds = $sportEquipmentBookeds->pluck('sportgeraet_id');
        $kursBookeIds = $sportEquipmentKursBookeds->pluck('sportgeraet_id');
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
            return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
        }

        $sportEquipmentBooked = new CourseParticipantBooked([
            'trainer_id' => Auth::user()->id,
            'kurs_id' => $coursedateId,
        ]);
        $sportEquipmentBooked->save();

        $userIds = DB::table('coursedate_user')->where('coursedate_id', $coursedateId)->pluck('user_id');
        DB::table('users')
            ->whereIn('id', $userIds)
            ->where('trainernachricht', '')
            ->update(['trainernachricht' => 1]);

        self::success('Teilnehmer wurde erfolgreich gebucht.');

        return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function equipmentBooked($coursedateId , $sportequipmentId)
    {
            $coursedate = Coursedate::find($coursedateId);
            if (!$coursedate) {
                self::warning('Termin wurde nicht gefunden.');
                return redirect()->route('backend.courseDate.index');
            }

            $organiser = $this->organiser();

            // Alle Sportgeräte
            $sportEquipments = CoursedateHelper::getSportEquipments($coursedate);

            // Belegte Sportgeräte andere Kurse
            $sportEquipmentBookeds = CoursedateHelper::getSportEquipmentBookeds($coursedate);

            // Gebuchte Sportgeräte für den Kurs
            $sportEquipmentKursBookeds = CoursedateHelper::getSportEquipmentKursBookeds($coursedate);

            $bookedIds = $sportEquipmentBookeds->pluck('sportgeraet_id');
            $kursBookeIds = $sportEquipmentKursBookeds->pluck('sportgeraet_id');
            $sportEquipmentPool = $sportEquipments->whereNotIn('id', $bookedIds);
            $sportEquipmentPool = $sportEquipmentPool->whereNotIn('id', $kursBookeIds);

            $simulatedSportEquipmentPool = $sportEquipmentPool->whereNotIn('id', $sportequipmentId)->values();

            $selectedEquipment = $sportEquipmentPool->firstWhere('id', (int) $sportequipmentId);
            if (!$selectedEquipment) {
                self::warning('Das gewählte Sportgerät ist nicht mehr frei verfügbar.');
                return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
            }

            $simulatedSportEquipmentKursBookeds = $sportEquipmentKursBookeds->push($selectedEquipment);
            $simulatedKursBookedPlaetze = (int) $simulatedSportEquipmentKursBookeds->sum('sportleranzahl');
            $simulatedKursBookedCount = (int) $simulatedSportEquipmentKursBookeds->count();

            $overlapingCoursedates = CoursedateHelper::getOverlappingCoursedates($coursedate);
            $overlapingCoursedates->push($coursedate);

            $overlapingCoursedatesWithParticipants = CoursedateHelper::getParticipantCountForOverlappingCoursedates($overlapingCoursedates)
                ->map(function ($row) use ($coursedate, $simulatedKursBookedPlaetze, $simulatedKursBookedCount) {
                    if ((int) ($row['coursedate_id'] ?? 0) !== (int) $coursedate->id) {
                        return $row;
                    }

                    $maxTeilnehmer = (int) ($row['maxTeilnehmer'] ?? 0);
                    $benoetigtePlaetze = max($maxTeilnehmer - $simulatedKursBookedPlaetze, 0);
                    $sportgeraetanzahl = (int) ($coursedate->sportgeraetanzahl ?? 0);
                    $benoetigtePlaetzeMax = $sportgeraetanzahl === 0
                        ? $benoetigtePlaetze
                        : min($benoetigtePlaetze, $sportgeraetanzahl);

                    $row['teilnehmerplaetzeGebuchteSportgeraete'] = $simulatedKursBookedPlaetze;
                    $row['gebuchteSportgeraeteAnzahl'] = $simulatedKursBookedCount;
                    $row['benoetigtePlaetze'] = $benoetigtePlaetze;
                    $row['benoetigtePlaetzeMax'] = $benoetigtePlaetzeMax;

                    return $row;
                })
                ->values();

            $allocationResult = CoursedateHelper::allocateFreeSportEquipmentGreedy(
                $overlapingCoursedatesWithParticipants,
                $simulatedSportEquipmentPool,
                $coursedate->id
            );

            $allocationItems = collect($allocationResult['items'] ?? []);
            $currentCoursedateAllocation = $allocationItems->firstWhere('coursedate_id', $coursedate->id);
            $allAllocationsHaveNoMissingPlaces = CoursedateHelper::allocationHasNoMissingPlaces($allocationResult);

            if (!$currentCoursedateAllocation || !$allAllocationsHaveNoMissingPlaces) {
                self::warning('Das '.$organiser->materialUeberschrift.' kann im aktuellen Termin nicht zugebucht werden, weil es nicht genug Platz für alle Teilnehmer gibt.');
                return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
            }

            $sportEquipmentBooked = new SportEquipmentBooked(
                [
                    'sportgeraet_id'    => $sportequipmentId,
                    'kurs_id'                => $coursedateId
                ]
            );

            $sportEquipmentBooked->save();

            $sportEquipmentKursBookedPlaetze = SportEquipmentBooked::join('sport_equipment', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
                ->where('sport_equipment_bookeds.kurs_id', $coursedateId)
                ->sum('sport_equipment.sportleranzahl');

            if ($coursedate->sportgeraetanzahl < $sportEquipmentKursBookedPlaetze) {
                $coursedate->update(
                    [
                        'sportgeraetanzahl' => $sportEquipmentKursBookedPlaetze,
                        'bearbeiter_id'     => Auth::user()->id,
                    ]
                );
                self::success('Anzahl der möglichen Teilnehmer erhöht.');
            }
            self::success('Sportgerät wurde erfolgreich gebucht.');

            return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function destroyBooked($coursedateId , $courseBookId)
    {
        $courseParticipantBooked = CourseParticipantBooked::find($courseBookId);

        $courseParticipantBooked->delete();

        $courseBookedCount = CourseParticipantBooked::where('kurs_id', $coursedateId)->count();

        if($courseBookedCount == 0)
        {
            $coursedate = Coursedate::find($coursedateId);
            $coursedate->update(
                [
                    'kursstarttermin'                 => $coursedate->kursstartvorschlag,
                    'kursendtermin'                  => $coursedate->kursendvorschlag,
                    'kursstartvorschlagkunde'  => $coursedate->kursstartvorschlag,
                    'kursendvorschlagkunde'   => $coursedate->kursendvorschlag,
                    'kursNichtDurchfuerbar'     => false
                ]
            );
        }

        self::success('Teilnehmer wurde erfolgreich gelöscht.');

        return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function equipmentBookedDestroy($coursedateId , $kursId , $sportgeraet)
    {
        //Es kann nicht direkt über die ID gelöscht werden, da die ID immer 1 ist.
        SportEquipmentBooked::join('sport_equipment', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->where('kurs_id', $kursId)
            ->where('sportgeraet', $sportgeraet)
            ->delete();

        self::success('Sportgerät wurde erfolgreich gelöscht.');

        return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function CourseBockedInformation($coursedateId)
    {
        $courseParticipantBookeds = CourseParticipantBooked::where('kurs_id', $coursedateId)
            ->get();

        return view('components.backend.courseDate.CourseBockedInformation', compact('courseParticipantBookeds'));
    }

    public function trainerRegister($coursedateId)
    {
        // Find the Coursedate by its ID
        $coursedate = Coursedate::find($coursedateId);

        // Attach the current logged-in user (trainer) to the Coursedate
        $coursedate->users()->attach(Auth::user()->id);

        self::success('Du hast dich als Trainer eingetragen.');

        return redirect()->route('backend.courseDate.indexAll');
    }

    public function trainerDestroy($coursedateId)
    {
        // Find the Coursedate by its ID
        $coursedate = Coursedate::find($coursedateId);

        // Detach the current logged-in user (trainer) from the Coursedate
        $coursedate->users()->detach(Auth::user()->id);

        self::success('Du hast dich als Trainer ausgetragen.');

        return redirect()->route('backend.courseDate.indexAll');
    }

    public function sportgeraetanzahlMaxCourse($id)
    {
        // Berechnung basierend auf Sportlerplätze - sum('sportleranzahl')
        $sportgeraetanzahlMax=Coursedate::join('course_sport_section', 'course_sport_section.course_id', '=', 'coursedates.course_id')
            ->join('sport_equipment', 'sport_equipment.sportSection_id', '=', 'course_sport_section.sport_section_id')
            ->where('coursedates.id', $id)
            ->sum('sport_equipment.sportleranzahl');

        return $sportgeraetanzahlMax;
    }

    public function cronJobPlanung()
    {
        $currentDate = Carbon::now();
        $currentYear = $currentDate->year;
        $trainings      = Training::where('datumbis', '>=', Carbon::now()->format('Y-m-d'))
            ->whereNull('deleted_at')
            ->get();

        foreach ($trainings as $training) {
            $courseDates = Coursedate::where('training_id', $training->id)
                ->whereYear('kursstartvorschlag', '!=', $currentYear) // Nur Termine außerhalb des laufenden Jahres
                ->orderBy('kursstartvorschlag')
                ->get();

            $newDate                   = Carbon::parse($training->datumvon);
            $kurslaenge                = Carbon::parse($training->zeitbis)->diff(Carbon::parse($training->zeitvon))->format('%H:%I:%S');
            $wiederholungAktuell = 0;
            $datumBerechnung    = Carbon::parse($training->datumAktuell);

            while ($newDate < $currentDate) {
                $newDate->addDays($training->wiederholung);
            }

            while ($newDate < $datumBerechnung) {
                $newDate->addDays($training->wiederholung);
                $wiederholungAktuell = $wiederholungAktuell+$training->wiederholung;
            }

            foreach ($courseDates as $courseDate) {
                if ($wiederholungAktuell >= $training->vorschauTage) {
                    break;
                }
                while ($wiederholungAktuell < $training->vorschauTage) {
                    if (Carbon::parse($courseDate->kursstartvorschlag) < $currentDate) {
                        $datumvon = Carbon::parse($newDate)->addSeconds(Carbon::parse($training->zeitvon)->diffInSeconds(Carbon::parse('00:00:00')));
                        $datumbis = Carbon::parse($newDate)->addSeconds(Carbon::parse($training->zeitbis)->diffInSeconds(Carbon::parse('00:00:00')));

                        $courseDateTestCount = Coursedate::where('kursstartvorschlag', $datumvon)
                            ->where('training_id', $courseDate->training_id)
                            ->count();

                        if ($courseDateTestCount == 0) {
                            $courseDate->update([
                                'organiser_id' => $training->organiser_id,
                                'course_id' => $training->course_id,
                                'kursstarttermin' => $datumvon,
                                'kursendtermin' => $datumbis,
                                'kursstartvorschlag' => $datumvon,
                                'kursendvorschlag' => $datumbis,
                                'kursstartvorschlagkunde' => $datumvon,
                                'kursendvorschlagkunde' => $datumbis,
                                'kurslaenge' => $kurslaenge,
                                'sportgeraetanzahl' => $training->sportgeraeteanzahl,
                                'sportgeraeteReserviert' => $training->sportgeraeteReserviert,
                                'bearbeiter_id' => $training->autor_id,
                            ]);
                        }
                    }
                    $newDate->addDays($training->wiederholung);
                    $wiederholungAktuell = $wiederholungAktuell + $training->wiederholung;
                }
            }

            $datumvon = now();
            while ($newDate <= Carbon::parse($training->datumbis)) {
                if($wiederholungAktuell >= $training->vorschauTage){
                    break;
                }

                $datumvon = Carbon::parse($newDate)->addSeconds(Carbon::parse($training->zeitvon)->diffInSeconds(Carbon::parse('00:00:00')));
                $datumbis  = Carbon::parse($newDate)->addSeconds(Carbon::parse($training->zeitbis)->diffInSeconds(Carbon::parse('00:00:00')));

                $courseDateTestCount = Coursedate::where('kursstartvorschlag', $datumvon)
                    ->where('training_id', $training->id)
                    ->count();

                if($courseDateTestCount == 0) {
                    $courseDateDelete = Coursedate::withTrashed()
                        ->where('training_id', '>', 0)
                        ->where('deleted_at' , '!=', null)
                        ->first();

                    if ($courseDateDelete) {
                        $courseDateDelete->restore();
                        $courseDateDelete->update([
                            'organiser_id' => $training->organiser_id,
                            'course_id' => $training->course_id,
                            'training_id' => $training->id,
                            'kursstarttermin' => $datumvon,
                            'kursendtermin' => $datumbis,
                            'kursstartvorschlag' => $datumvon,
                            'kursendvorschlag' => $datumbis,
                            'kursstartvorschlagkunde' => $datumvon,
                            'kursendvorschlagkunde' => $datumbis,
                            'kurslaenge' => $kurslaenge,
                            'sportgeraetanzahl' => $training->sportgeraeteanzahl,
                            'sportgeraeteReserviert' => $training->sportgeraeteReserviert,
                            'bearbeiter_id' => $training->bearbeiter_id,
                            'autor_id' => $training->bearbeiter_id,
                            'created_at' => Carbon::now()
                        ]);
                    }
                    else{
                        Coursedate::create([
                            'organiser_id' => $training->organiser_id,
                            'training_id' => $training->id,
                            'course_id' => $training->course_id,
                            'kursstarttermin' => $datumvon,
                            'kursendtermin' => $datumbis,
                            'kursstartvorschlag' => $datumvon,
                            'kursendvorschlag' => $datumbis,
                            'kursstartvorschlagkunde' => $datumvon,
                            'kursendvorschlagkunde' => $datumbis,
                            'kurslaenge' => $kurslaenge,
                            'sportgeraetanzahl' => $training->sportgeraeteanzahl,
                            'sportgeraeteReserviert' => $training->sportgeraeteReserviert,
                            'bearbeiter_id' => $training->bearbeiter_id,
                            'autor_id' => $training->bearbeiter_id,
                        ]);
                    }
                }

                $newDate->addDays($training->wiederholung);
                $wiederholungAktuell += $training->wiederholung;
            }

            $training->update([
                'datumAktuell' => $datumvon
            ]);
        }
    }
}
