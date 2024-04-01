<?php

namespace App\Http\Controllers\CourseBooking;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCourseParticipantRequest;
use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use App\Models\Organiser;
use App\Models\SportEquipment;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CourseParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organiser = $this->organiser();

        $coursedates = Coursedate::where('coursedates.organiser_id', $organiser->id)
            // ToDo:Vorher Filter so das nur noch Ergebnisse vorhanden sind die den angemeldeten Trainer zugeordnet sind
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
            ->paginate(10);

        return view('components.courseBooking.course.index', compact('coursedates', 'organiser'));
    }

    public function indexParticipant()
    {
        $organiser = $this->organiser();

        $coursedates = Coursedate::where('coursedates.organiser_id', $organiser->id)
            // ToDo:Vorher Filter so das nur noch Ergebnisse vorhanden sind die den angemeldeten Trainer zugeordnet sind
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
            ->distinct('coursedates.id')
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

        $courseBookeAlls = CourseParticipantBooked::where('kurs_id', $id)
            ->get();

        $courseBookeAlls = $courseBookeAlls->diff($courseBookes);

        $teilnehmerKursBookeds = CourseParticipantBooked::where('kurs_id', '<>' , $id)
            ->join('coursedates', 'coursedates.id', '=', 'course_participant_bookeds.kurs_id')
            ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            ->join('users', 'users.id', '=', 'coursedate_user.user_id')
            ->where('course_participant_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
            ->get();

        $sportEquipments = Coursedate::join('course_sport_section', 'course_sport_section.course_id', '=', 'coursedates.course_id')
            ->join('sport_equipment', 'sport_equipment.sportSection_id', '=', 'course_sport_section.sport_section_id')
            ->where('coursedates.id', $coursedate->id)
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

        // Belegte Boote andere Kurse
        $sportEquipmentBookeds = SportEquipment::join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            ->join('users', 'users.id', '=', 'coursedate_user.user_id')
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
            ->whereNot('sport_equipment_bookeds.kurs_id', $coursedate->id)
            ->orderBy('sport_equipment.sportgeraet')
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

        $bookedIds = $sportEquipmentBookeds->pluck('sportgeraet_id');
        $kursBbookeIds =  $sportEquipmentKursBookeds->pluck('sportgeraet_id');
        $sportEquipments= $sportEquipments->whereNotIn('id', $bookedIds);
        $sportEquipments= $sportEquipments->whereNotIn('id', $kursBbookeIds);

        $freeSportEquipment =$sportEquipmentBookeds->count()-$teilnehmerKursBookeds->count();
        if($freeSportEquipment>0){
            $freeSportEquipment=0;
        }

        if($coursedate->sportgeraetanzahl==0) {
            $sportgeraetanzahlMax = $sportEquipments->count()+$sportEquipmentKursBookeds->count()-$courseBookes->count()-$courseBookeAlls->count()+$freeSportEquipment;
        }
        else {
            if($sportEquipments->count()+$sportEquipmentKursBookeds->count()>$coursedate->sportgeraetanzahl) {
                $sportgeraetanzahlMax = $coursedate->sportgeraetanzahl-$courseBookes->count()-$courseBookeAlls->count();
            }
            else {
                $sportgeraetanzahlMax = $sportEquipments->count();
            }
        }
        $timeMin=Carbon::parse($coursedate->kursstartvorschlag)->format('H:i');
        $courseLengthInMinutes = Carbon::parse($coursedate->kurslaenge);
        $timeMax = Carbon::parse($coursedate->kursendvorschlag)->addMinutes($courseLengthInMinutes)->format('H:i');

        return view('components.courseBooking.course.edit', compact([
                'coursedate',
                'sportgeraetanzahlMax',
                'organiser',
                'courseBookes',
                'courseBookeAlls',
                'timeMax',
                'timeMin'
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

        $coursedate->update(
            [
                'kursstarttermin'         => $daten['kursstarttermin'],
                'kursendtermin'           => $daten['kursendtermin'],
                'kursstartvorschlagkunde' => $daten['kursstarttermin'],
                'kursendvorschlagkunde'   => $daten['kursendtermin'],
           ]
        );

        $this->book($coursedate->id);

        self::success('Die Zeit für den Termin wurde angepasst.');

        return redirect()->route('courseBooking.course.edit', $coursedate->id);
    }

    public function book($coursedateId)
    {
        $coursedate = Coursedate::find($coursedateId);
        $bookedCount=$this->bookedCount($coursedate);

        if($bookedCount['sportgeraetanzahlMax'] - $bookedCount['courseBookesCount'] >= 1) {
            $sportEquipmentBooked = new CourseParticipantBooked(
                [
                    'participant_id' => Auth::user()->id,
                    'kurs_id' => $coursedateId,
                    //'user_id'           => Auth::user()->id,
                    //'bearbeiter_id'     => Auth::user()->id,
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now()
                ]
            );

            $sportEquipmentBooked->save();

            self::success('Ein Teilnehmer wurde erfolgreich gebucht.');
        }
        else {
            self::warning('Die maximale Anzahl an Teilnehmer ist erreicht. Es können keine weiteren Teilnehmer gebucht werden.');
        }

        return redirect()->route('courseBooking.course.edit', $coursedateId);
    }

    public function destroyBooked($coursedateId , $courseBookId)
    {
        $sportEquipmentBooked = CourseParticipantBooked::find($courseBookId);

        $sportEquipmentBooked->delete();

        $courseBookeCount = CourseParticipantBooked::where('kurs_id', $coursedateId)->count();

        if($courseBookeCount == 0)
        {
            $coursedate = Coursedate::find($coursedateId);
            $coursedate->update(
                [
                    'kursstarttermin'         => $coursedate->kursstartvorschlag,
                    'kursendtermin'           => $coursedate->kursendvorschlag,
                    'kursstartvorschlagkunde' => $coursedate->kursstartvorschlag,
                    'kursendvorschlagkunde'   => $coursedate->kursendvorschlag
                ]
            );
        }

        self::success('Ein Teilnehmer wurde erfolgreich storniert.');

        return redirect()->route('courseBooking.course.edit', $coursedateId);
    }

    public function organiserDomainId()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        return $organiser->id;
    }

    public function organiser()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        return $organiser;
    }

    public function bookedCount($coursedate)
    {
        //ToDo: Auf Sportplätze umstellen ->sum('sportleranzahl');
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
            ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            ->join('users', 'users.id', '=', 'coursedate_user.user_id')
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
            ->whereNot('sport_equipment_bookeds.kurs_id', $coursedate->id)
            ->orderBy('sport_equipment.sportgeraet')
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

    public function kursendtermin($request, $coursedate)
    {
        $kursstarttermin  = Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d');
        $date             = Carbon::parse($kursstarttermin.' '.$request->kursstartterminTime);
        $time             = Carbon::parse($coursedate->kurslaenge);
        $hours            = $time->hour;
        $minutes          = $time->minute;
        $kursendtermin    = Carbon::parse($kursstarttermin.' '.$request->kursstartterminTime)->addHours($hours)->addMinutes($minutes);
        return [
            'kursstarttermin' => $date,
            'kursendtermin'   => $kursendtermin,
       ];
    }
}
