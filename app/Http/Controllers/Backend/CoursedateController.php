<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Coursedate;
use App\Http\Requests\StoreCoursedateRequest;
use App\Http\Requests\UpdateCoursedateRequest;
use App\Models\CourseParticipantBooked;
use App\Models\Organiser;
use App\Models\SportEquipment;
use App\Models\SportEquipmentBooked;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

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
            // ToDo:Vorher Filter das nur noch Ergebnisse vorhanden sind die den angemeldeten Trainer zugeordnet sind
            // Aktuel wird das in der blade mit einer if Abfrage gemacht
            //->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            //->where('coursedate_user.user_id', Auth::user()->id)
            ->withCount(['courseParticipantBookeds as booked_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id');
            }])
            ->orderBy('kursstarttermin')
            ->paginate(10);

        return view('components.backend.courseDate.index', compact('coursedates', 'organiser'));
    }

    public function indexAll()
    {
        $organiser = $this->organiser();

        $coursedates = Coursedate::where('organiser_id', $organiser->id)
            ->leftJoin('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->whereNull('course_participant_bookeds.deleted_at')
            ->withCount(['courseParticipantBookeds as booked_count' => function ($query) {
                $query->whereColumn('kurs_id', 'coursedates.id');
            }])
            ->distinct()
            ->orderBy('kursstarttermin')
            ->paginate(10);

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

        $courses = Course::where('organiser_id' , $organiser->id)
            ->orderBy('kursName')
            ->get();

        $course_id = 0;
        $sportgeraetanzahl = 0;
        $sportgeraetanzahlMax = $this->sportgeraetanzahlMax($organiser->id);

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
        // ToDo: Valedierung anpassen

        $message=[
            '1' => 'Der Termin wurde erfolgreich angelegt.',
            '2' => 'Der Endtermin wurde neu berechnet und der Termin wurde erfolgreich angelegt.'
        ];
        $daten=$this->kursendtermin($request, $message);
        self::success($daten['message']);

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
                'updated_at'              => Carbon::now(),
                'created_at'              => Carbon::now()
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

        $sportgeraetanzahlMax = $this->sportgeraetanzahlMax($coursedate->organiser_id);

        return view('components.backend.courseDate.edit', compact([
            'coursedate',
            'sportgeraetanzahlMax',
            'courses',
            'organiser'
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
        // ToDo: Valedierung anpassen

        $message=[
            '1' => 'Der Termin wurde erfolgreich bearbeitet.',
            '2' => 'Der Endtermin wurde neu berechnet und der Termin erfolgreich gespeichert.'
        ];
        $daten=$this->kursendtermin($request, $message);
        self::success($daten['message']);

        $coursedate->update(
            [
                'course_id'               => $request->course_id,
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
                'updated_at'              => Carbon::now()
            ]
        );

        return redirect()->route('backend.courseDate.index');
    }

    public function updateBooked(UpdateCoursedateRequest $request, Coursedate $coursedate)
    {
        // ToDo: Valedierung anpassen
        self::success('Der Termin wurde erfolgreich bearbeitet.');

        $coursedate->update(
            [
                'sportgeraetanzahl'       => $request->sportgeraetanzahl,
                'kursInformation'         => $request->kursInformation,
                'bearbeiter_id'           => Auth::user()->id,
                'updated_at'              => Carbon::now()
            ]
        );

        return redirect()->route('backend.courseDate.index');
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
        $coursedate = Coursedate::find($id);

        $course = Course::find($coursedate->course_id);

        $trainers = User::Join('coursedate_user', 'coursedate_user.user_id', '=', 'users.id')
              ->where('coursedate_user.coursedate_id', $coursedate->id)
              ->get();

        $courseBookes = CourseParticipantBooked::where('kurs_id', $id)->get();

        $teilnehmerKursBookeds = CourseParticipantBooked::where('kurs_id', '<>' , $id)
            ->join('coursedates', 'coursedates.id', '=', 'course_participant_bookeds.kurs_id')
            ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            ->join('users', 'users.id', '=', 'coursedate_user.user_id')
            //->where('course_participant_bookeds.trainer_id', '<>', 0)
            ->where('course_participant_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
            ->get();

        // Alle Sportgeräte
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

        $bookedIds           = $sportEquipmentBookeds->pluck('sportgeraet_id');
        $kursBbookeIds       = $sportEquipmentKursBookeds->pluck('sportgeraet_id');
        $sportEquipmentFrees = $sportEquipments->whereNotIn('id', $bookedIds);
        $sportEquipmentFrees = $sportEquipmentFrees->whereNotIn('id', $kursBbookeIds);

        if($coursedate->sportgeraetanzahl==0) {
            $sportgeraetanzahlMax = $sportEquipmentFrees->count()+$sportEquipmentKursBookeds->count()-$courseBookes->count();
        }
        else {
            $sportgeraetanzahlMax = $coursedate->sportgeraetanzahl-$courseBookes->count();
            if($sportgeraetanzahlMax>$sportEquipmentFrees->count()+$sportEquipmentKursBookeds->count()) {
                $sportgeraetanzahlMax = $sportEquipmentFrees->count();
            }
        }

        return view('components.backend.courseDate.sportingSequipment', compact([
            'coursedate',
            'course',
            'sportEquipmentFrees',
            'sportEquipmentKursBookeds',
            'sportEquipmentBookeds',
            'courseBookes',
            'teilnehmerKursBookeds',
            'sportgeraetanzahlMax',
            'trainers'
        ]));
    }

    public function Book($coursedateId)
    {
        $sportEquipmentBooked = new CourseParticipantBooked(
            [
                'trainer_id'        => Auth::user()->id,
                'kurs_id'           => $coursedateId,
                'user_id'           => Auth::user()->id,
                'bearbeiter_id'     => Auth::user()->id,
                'updated_at'        => Carbon::now(),
                'created_at'        => Carbon::now()
            ]
        );

        $sportEquipmentBooked->save();

        self::success('Teilnehmer wurde erfolgreich gebucht.');

        return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function equipmentBooked($coursedateId , $sportequipmentId)
    {
            $sportEquipmentBooked = new SportEquipmentBooked(
                [
                    'sportgeraet_id'    => $sportequipmentId,
                    'kurs_id'           => $coursedateId,
                    'user_id'           => Auth::user()->id,
                    'bearbeiter_id'     => Auth::user()->id,
                    'updated_at'        => Carbon::now(),
                    'created_at'        => Carbon::now()
                ]
            );

            $sportEquipmentBooked->save();

            $coursedate = Coursedate::find($coursedateId);
            if($coursedate->sportgeraetanzahl != 0) {
                $sportEquipmentKursBookedCount = SportEquipmentBooked::where('kurs_id', $coursedateId)->count();
                if ($coursedate->sportgeraetanzahl < $sportEquipmentKursBookedCount) {
                    $coursedate->update(
                        [
                            'sportgeraetanzahl' => $sportEquipmentKursBookedCount,
                            'bearbeiter_id'     => Auth::user()->id,
                            'updated_at'        => Carbon::now()
                        ]
                    );
                    self::success('Anzahl der möglichen Teilnehmer erhöht.');
                }
            }
            self::success('Sportgerät wurde erfolgreich gebucht.');

            return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function destroyBooked($coursedateId , $courseBookId)
    {
        $sportEquipmentBooked = CourseParticipantBooked::find($courseBookId);

        $sportEquipmentBooked->delete();

        self::success('Teilnehmer wurde erfolgreich gelöscht.');

        return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function equipmentBookedDestroy($coursedateId , $kursId , $sportgeraet)
    {
        //ToDo: Es kann nicht direkt über die ID gelöscht werden, da die ID immer 1 ist.
        $sportEquipmentBooked = SportEquipmentBooked::
              join('sport_equipment', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->where('kurs_id', $kursId)
            ->where('sportgeraet', $sportgeraet)
            ->delete();

        self::success('Sportgerät wurde erfolgreich gelöscht.');

        return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function trainerRegister($coursedateId)
    {
        // Find the Coursedate by its ID
        $coursedate = Coursedate::find($coursedateId);

        // Attach the current logged in user (trainer) to the Coursedate
        $coursedate->users()->attach(Auth::user()->id);

        self::success('Du hast dich als Trainer eingetragen.');

        return redirect()->route('backend.courseDate.indexAll');
    }

    public function trainerDestroy($coursedateId)
    {
        // Find the Coursedate by its ID
        $coursedate = Coursedate::find($coursedateId);

        // Detach the current logged in user (trainer) from the Coursedate
        $coursedate->users()->detach(Auth::user()->id);

        self::success('Du hast dich als Trainer ausgetragen.');

        return redirect()->route('backend.courseDate.indexAll');
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

    public function organiserDomainId()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        return $organiser->id;
    }

    public function sportgeraetanzahlMaxCourse($id)
    {
        //ToDo: Auf Spotzplätze umstellen ->sum('sportleranzahl');
        $sportgeraetanzahlMax=Coursedate::join('course_sport_section', 'course_sport_section.course_id', '=', 'coursedates.course_id')
            ->join('sport_equipment', 'sport_equipment.sportSection_id', '=', 'course_sport_section.sport_section_id')
            ->where('coursedates.id', $id)
            ->orderBy('sport_equipment.sportgeraet')
            ->count();

        return $sportgeraetanzahlMax;
    }

    public function sportgeraetanzahlMax($id)
    {
        //ToDo: Auf Spotzplätze umstellen ->sum('sportleranzahl');
        $sportgeraetanzahlMax = SportEquipment::join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
            ->where('organiser_sport_section.organiser_id' , $id)
            ->count();

        return $sportgeraetanzahlMax;
    }

    public function kursendtermin($request, $message)
    {
        $date                  = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime);
        $time                  = Carbon::parse($request->kurslaenge);

        $hours                 = $time->hour;
        $minutes               = $time->minute;
        $kurslaeneminuten      = $hours*60 + $minutes;
        $kursenddatum          = Carbon::parse($request->kursendterminDatum.' '.$request->kursendterminTime);
        $diffMinute            = $date->diffInMinutes($kursenddatum);

        if($kursenddatum < $date) {
            $diffMinute = $diffMinute * -1;
        }

        if($diffMinute < $kurslaeneminuten) {
            $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hours)->addMinutes($minutes);
            $message=$message[2];
        }
        else {
            $kursendtermin = $request->kursendterminDatum.' '.$request->kursendterminTime;
            $message=$message[1];
        }

        return [
            'kursstarttermin' => $date,
            'kursendtermin'   => $kursendtermin,
            'message'         => $message,
        ];
   }
}
