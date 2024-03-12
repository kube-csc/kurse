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
        $coursedates = Coursedate::where('coursedates.organiser_id', $this->organiserDomainId())
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            // ToDo:Vorher Filter das nur noch Ergebnisse vorhanden sind die der Angemeldenten Trainer zugeordnet sind
            // Aktuel wird das in der blade mit einer if Abfrage gemacht
            //->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            //->where('coursedate_user.user_id', Auth::user()->id)
            ->orderBy('kursstarttermin')
            ->paginate(10);

        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        return view('components.backend.courseDate.index', compact('coursedates', 'organiser'));
    }

    public function indexAll()
    {
        $coursedates = Coursedate::where('organiser_id', $this->organiserDomainId())
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->orderBy('kursstarttermin')
            ->paginate(10);

        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        return view('components.backend.courseDate.indexAll', compact('coursedates', 'organiser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kursstartterminDatum = Carbon::now()->format('Y-m-d');
        $kursstartterminTime = Carbon::now()->format('H:i');
        $kurslaengeStunde = '01';
        $kurslaengeMinute = '30';
        $kurslaenge = $kurslaengeStunde.':'.$kurslaengeMinute;
        $kursendterminDatum=$kursstartterminDatum;
        $kursendterminTime = Carbon::now()->addHours($kurslaengeStunde)->addMinutes($kurslaengeMinute)->format('H:i');

        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        $courses = Course::where('organiser_id' , $this->organiserDomainId())
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

        // Parse the date and time to Carbon instances
        $date = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime);
        $time = Carbon::parse($request->kurslaenge);
        // Extract the hours and minutes from the time
        $hours = $time->hour;
        $minutes = $time->minute;
        $kurslaeneminuten =  $hours*60 + $minutes;
        $hoursStart = Carbon::parse($request->kursstartterminTime)->hour;
        $minutesStart = Carbon::parse($request->kursstartterminTime)->minute;
        $kurslaeneminutenStart =  $hoursStart*60 + $minutesStart;

        // Parse the dates to Carbon instances
        $kursendterminBerechnung = Carbon::parse($request->kursendterminDatum.' '.$request->kursendterminTime);
        $kursstarttermin = Carbon::parse($request->kursstarttermin.' '.$request->kursstartterminTime);
        // Calculate the difference in minutes
        // $diffDay = $kursendterminBerechnung->diffInDays($kursstarttermin);
        $kursendterminBerechnungDatum = Carbon::parse($request->kursendtermin)->format('Y-m-d');
        $kursstartterminDatum = Carbon::parse($request->kursstarttermin)->format('Y-m-d');
        $diffMinute = $kursendterminBerechnung->diffInMinutes($kursstarttermin);

        if($kursendterminBerechnungDatum <= $kursstartterminDatum)
        {
            if ($kurslaeneminuten > $kurslaeneminutenStart)
            {
                $diffMinute = $diffMinute * 1;
            }
            else
            {
                $diffMinute = $diffMinute * -1;
            }
        }

        if($diffMinute< $kurslaeneminuten)
         {
             // FixMe: Self::danger('... funktioniert nicht $dangeer = wurde als alternative verwendet
             //self::danger('Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.');
             $danger = 'Der Endtermin wurde neu berechnet und erfolgreich gespeichert angelegt.';

             if($kurslaeneminutenStart+$kurslaeneminuten >= 1440)
             {
                 $hoursAdd = $hours+0;
                 $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hoursAdd)->addMinutes($minutes);
             }
             else
             {
                 $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hours)->addMinutes($minutes);
             }

             self::warning('Der Endtermin wurde neu berechnet und erfolgreich gespeichert angelegt.');
         }
        else
         {
            $kursendtermin = $request->kursendterminDatum.' '.$request->kursendterminTime;
            self::success('Der Termin wurde erfolgreich angelegt.');
         }

        $coursedate = new coursedate(
            [
                'course_id'               => $request->course_id,
                'organiser_id'            => $this->organiserDomainId(),
                'kurslaenge'              => $request->kurslaenge,
                'kursstarttermin'         => $date,
                'kursendtermin'           => $kursendtermin,
                'kursstartvorschlag'      => $date,
                'kursendvorschlag'        => $kursendtermin,
                'kursstartvorschlagkunde' => $date,
                'kursendvorschlagkunde'   => $kursendtermin,
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

        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        $courses = Course::where('organiser_id' , $organiser->id)
            ->orderBy('kursName')
            ->get();

        $sportgeraetanzahlMax = $this->sportgeraetanzahlMaxCourse($coursedate->id);

        return view('components.backend.courseDate.edit', compact([
            'coursedate',
            'sportgeraetanzahlMax',
            'courses',
            'organiser'
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCoursedateRequest $request, Coursedate $coursedate)
    {
        //$coursedate->update($request->validated());

        // Parse the date and time to Carbon instances
        $date = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime);
        $time = Carbon::parse($request->kurslaenge);
        // Extract the hours and minutes from the time
        $hours = $time->hour;
        $minutes = $time->minute;
        $kurslaeneminuten =  $hours*60 + $minutes;
        $hoursStart = Carbon::parse($request->kursstartterminTime)->hour;
        $minutesStart = Carbon::parse($request->kursstartterminTime)->minute;
        $kurslaeneminutenStart =  $hoursStart*60 + $minutesStart;

        // Parse the dates to Carbon instances
        $kursendterminBerechnung = Carbon::parse($request->kursendterminDatum.' '.$request->kursendterminTime);
        $kursstarttermin = Carbon::parse($request->kursstarttermin.' '.$request->kursstartterminTime);
        // Calculate the difference in minutes
        $kursendterminBerechnungDatum = Carbon::parse($request->kursendtermin)->format('Y-m-d');
        $kursstartterminDatum = Carbon::parse($request->kursstarttermin)->format('Y-m-d');
        $diffMinute = $kursendterminBerechnung->diffInMinutes($kursstarttermin);

        if($kursendterminBerechnungDatum <= $kursstartterminDatum)
        {
            if ($kurslaeneminuten > $kurslaeneminutenStart)
            {
                $diffMinute = $diffMinute * 1;
            }
            else
            {
                $diffMinute = $diffMinute * -1;
            }
        }

        if($diffMinute < $kurslaeneminuten)
        {
            // FixMe: Self::danger('... funktioniert nicht $dangeer = wurde als alternative verwendet
            //self::danger('Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.');
            $danger = 'Der Endtermin wurde neu berechnet und erfolgreich gespeichert angelegt.';

            if($kurslaeneminutenStart+$kurslaeneminuten >= 1440)
            {
                $hoursAdd = $hours+0;
                $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hoursAdd)->addMinutes($minutes);
            }
            else
            {
                $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hours)->addMinutes($minutes);
            }

            self::warning('Der Endtermin wurde neu berechnet und erfolgreich gespeichert bearbeitet.');
         }
        else
        {
            $kursendtermin = $request->kursendterminDatum.' '.$request->kursendterminTime;
            self::success('Der Termin wurde erfolgreich bearbeitet.');
        }

        $coursedate->update(
            [
                'course_id'               => $request->course_id,
                'kurslaenge'              => $request->kurslaenge,
                'kursstarttermin'         => $date,
                'kursendtermin'           => $kursendtermin,
                'kursstartvorschlag'      => $date,
                'kursendvorschlag'        => $kursendtermin,
                'kursstartvorschlagkunde' => $date,
                'kursendvorschlagkunde'   => $kursendtermin,
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

        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        $sportEquipments = Coursedate::join('course_sport_section', 'course_sport_section.course_id', '=', 'coursedates.course_id')
            ->join('sport_equipment', 'sport_equipment.sportSection_id', '=', 'course_sport_section.sport_section_id')
            ->where('coursedates.id', $coursedate->id)
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

        $couseBookes = CourseParticipantBooked::where('kurs_id', $id)->get();

        $teilnehmerKursBookeds = CourseParticipantBooked::where('kurs_id', '<>' , $id)
            ->join('coursedates', 'coursedates.id', '=', 'course_participant_bookeds.kurs_id')
            ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
            ->join('users', 'users.id', '=', 'coursedate_user.user_id')
            ->where('course_participant_bookeds.trainer_id', '<>', 0)
            ->where('course_participant_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
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
            $sportgeraetanzahlMax = $sportEquipments->count()+$sportEquipmentKursBookeds->count()-$couseBookes->count()+$freeSportEquipment;
        }
        else {
            if($sportEquipments->count()+$sportEquipmentKursBookeds->count()>$coursedate->sportgeraetanzahl) {
                $sportgeraetanzahlMax = $coursedate->sportgeraetanzahl-$couseBookes->count();
            }
            else {
                  $sportgeraetanzahlMax = $sportEquipments->count();
            }
        }

        return view('components.backend.courseDate.sportingSequipment', compact([
            'coursedate',
            'course',
            'sportEquipments',
            'sportEquipmentKursBookeds',
            'sportEquipmentBookeds',
            'couseBookes',
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

            self::success('Sportgerät wurde erfolgreich gebucht.');

            return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

    public function destroyBooked($coursedateId , $couseBookId)
    {
        $sportEquipmentBooked = CourseParticipantBooked::find($couseBookId);

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
        //ToDo: Aud Potzplätze umstellen ->sum('sportleranzahl');
        $sportgeraetanzahlMax = SportEquipment::
        join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
            ->where('organiser_sport_section.organiser_id' , $id)
            ->count();

        return $sportgeraetanzahlMax;
    }
}
