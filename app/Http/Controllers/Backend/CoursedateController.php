<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Coursedate;
use App\Http\Requests\StoreCoursedateRequest;
use App\Http\Requests\UpdateCoursedateRequest;
use App\Models\SportEquipment;
use App\Models\SportEquipmentBooked;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class CoursedateController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coursedates = Coursedate::where('sportSection_id', env('KURS_ABTEILUNG', 1))
            ->where('trainer_id', Auth::user()->id)
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->orderBy('kursstarttermin')
            ->paginate(10);

        return view('components.backend.courseDate.index', compact('coursedates'));
    }

    public function indexAll()
    {
        $coursedates = Coursedate::where('sportSection_id', env('KURS_ABTEILUNG', 1))
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->orderBy('kursstarttermin')
            ->paginate(10);

        return view('components.backend.courseDate.index', compact('coursedates'));
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

        $courses = Course::where('sportSection_id' , env('KURS_ABTEILUNG', 1))
            ->orderByDesc('kursName')
            ->get();

        $course_id = 0;
        $sportgeraetanzahl = 0;
        $sportgeraetanzahlMax = $this->sportgeraetanzahlMax();

        return view('components.backend.courseDate.create' , compact([
            'kursstartterminDatum',
            'kursstartterminTime',
            'kurslaenge',
            'kursendterminDatum',
            'kursendterminTime',
            'sportgeraetanzahlMax',
            'sportgeraetanzahl',
            'courses',
            'course_id'
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
             $danger = 'Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.
             Der Kurs Endtermin wurde automatisch berechnet. Bitte überprüfe die Daten nochmal.';

             // ToDo: Wird die If-Abfrage benötigt?
             if($kurslaeneminutenStart+$kurslaeneminuten >= 1440)
             {
                 $hoursAdd = $hours+0;
                 $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hoursAdd)->addMinutes($minutes);
             }
             else
             {
                 $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hours)->addMinutes($minutes);
             }

             self::warning('Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.
             Der Kurs Endtermin wurde automatisch berechnet wurde erfolgreich angelegt');
         }
        else
         {
            $kursendtermin = $request->kursendterminDatum.' '.$request->kursendterminTime;
            self::success('Kurstermin wurde erfolgreich angelegt.');
         }

        $coursedate = new coursedate(
            [
                'trainer_id'         => Auth::user()->id,
                'sportSection_id'    => env('KURS_ABTEILUNG', 1),
                'course_id'          => $request->course_id,
                'kurslaenge'         => $request->kurslaenge,
                'kursstarttermin'    => $date,
                'kursendtermin'      => $kursendtermin,
                'kursstartvorschlag' => $date,
                'kursendvorschlag'   => $kursendtermin,
                'sportgeraetanzahl'  => $request->sportgeraetanzahl,
                'bearbeiter_id'      => Auth::user()->id,
                'user_id'            => Auth::user()->id,
                'updated_at'         => Carbon::now(),
                'created_at'         => Carbon::now()
            ]
        );
        $coursedate->save();

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

        $courses = Course::where('sportSection_id' , env('KURS_ABTEILUNG', 1))
            ->orderByDesc('kursName')
            ->get();

        $sportgeraetanzahlMax = $this->sportgeraetanzahlMax();

        return view('components.backend.courseDate.edit', compact([
            'coursedate',
            'sportgeraetanzahlMax',
            'courses'
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
            $danger = 'Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.
             Der Kurs Endtermin wurde automatisch berechnet. Bitte überprüfe die Daten nochmal.';

            // ToDo: Wird die If-Abfrage benötigt?
            if($kurslaeneminutenStart+$kurslaeneminuten >= 1440)
            {
                $hoursAdd = $hours+0;
                $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hoursAdd)->addMinutes($minutes);
            }
            else
            {
                $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hours)->addMinutes($minutes);
            }

            self::warning('Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.');
         }
        else
        {
            $kursendtermin = $request->kursendterminDatum.' '.$request->kursendterminTime;
            self::success('Kurstermin wurde erfolgreich bearbeitet.');
        }

        $coursedate->update(
            [
                'course_id'          => $request->course_id,
                'kurslaenge'         => $request->kurslaenge,
                'kursstarttermin'    => $date,
                'kursendtermin'      => $kursendtermin,
                'kursstartvorschlag' => $date,
                'kursendvorschlag'   => $kursendtermin,
                'sportgeraetanzahl'  => $request->sportgeraetanzahl,
                'bearbeiter_id'      => Auth::user()->id,
                'updated_at'         => Carbon::now()
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

        self::success('Kurstermin wurde erfolgreich gelöscht.');

        return redirect()->route('backend.courseDate.index');
    }

    public function sportgeraetanzahlMax()
    {
        $sportgeraetanzahlMax = SportEquipment::where('sportSection_id' , env('KURS_ABTEILUNG',1))->count(); //ToDo - aus der Datenbank holen
        return $sportgeraetanzahlMax;
    }

    public function sportingEquipment($id)
    {
        $coursedate = Coursedate::find($id);

        $course = Course::find($coursedate->course_id);

        $sportEquipmentVerfuegbars = SportEquipment::where('sportSection_id' , env('KURS_ABTEILUNG',1))
            ->orderBy('sportgeraet')
            ->get();

        $couseBookes = SportEquipmentBooked::where('kurs_id', $id)->get();

        $sportEquipmentBookeds  = SportEquipment::where('sport_equipment.sportSection_id' , env('KURS_ABTEILUNG',1))
            ->join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->join('users', 'users.id', '=', 'coursedates.trainer_id')
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
            ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
            ->orderBy('sport_equipment.sportgeraet')
            ->get();

        $bookedIds = $sportEquipmentBookeds->pluck('sportgeraet');
        $sportEquipments= $sportEquipmentVerfuegbars->whereNotIn('sportgeraet', $bookedIds);

        if($coursedate->sportgeraetanzahl==0) {
            $sportgeraetanzahlMax = $sportEquipments->count();
        }
        else {
            if($sportEquipments->count()>$coursedate->sportgeraetanzahl) {
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
            'sportEquipmentBookeds',
            'couseBookes',
            'sportgeraetanzahlMax'
        ]));
    }

    public function Book($coursedateId , $sportequipmentId)
    {
        $sportEquipmentBooked = new SportEquipmentBooked(
            [
                'sportgeraet_id'    => $sportequipmentId,
                'trainer_id'        => Auth::user()->id,
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

    //Programier das löschen von Teilnehmern
    public function destroyBooked($coursedateId , $couseBookId)
    {
        $sportEquipmentBooked = SportEquipmentBooked::find($couseBookId);

        $sportEquipmentBooked->delete();

        self::success('Sportgerät wurde erfolgreich gelöscht.');

        return redirect()->route('backend.courseDate.sportingEquipment', $coursedateId);
    }

}
