<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Coursedate;
use App\Http\Requests\StoreCoursedateRequest;
use App\Http\Requests\UpdateCoursedateRequest;
use App\Models\SportEquipment;
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
            ->paginate(10);

        return view('components.backend.courseDate.index', compact('coursedates'));
    }

    public function indexAll()
    {
        $coursedates = Coursedate::where('sportSection_id', env('KURS_ABTEILUNG', 1))
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->paginate(10);

        return view('components.backend.courseDate.index', compact('coursedates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kursstarttermin = Carbon::now()->format('Y-m-d H:i');
        $kurslaengeStunde = '01';
        $kurslaengeMinute = '00';
        $kurslaenge = $kurslaengeStunde.':'.$kurslaengeMinute;
        $kursendtermin = Carbon::now()->addHours($kurslaengeStunde)->addMinutes($kurslaengeMinute)->format('Y-m-d H:i');

        $courses = Course::where('sportSection_id' , env('KURS_ABTEILUNG', 1))
            ->orderByDesc('kursName')
            ->get();

        $course_id = 0;
        $sportgeraetanzahl = 0;
        $sportgeraetanzahlMax = $this->sportgeraetanzahlMax();

        return view('components.backend.courseDate.create' , compact([
            'kursstarttermin',
            'kurslaenge',
            'kursendtermin',
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
        $data = $request->validated();

        // Parse the date and time to Carbon instances
        $date = Carbon::parse($request->kursstarttermin);
        $time = Carbon::parse($request->kurslaenge);
        // Extract the hours and minutes from the time
        $hours = $time->hour;
        $minutes = $time->minute;
        $kurslaeneminuten =  $hours*60 + $minutes;
        $kursendtermin = $date->addHours($hours)->addMinutes($minutes);

        // Parse the dates to Carbon instances
        $kursendterminBerechnung = Carbon::parse($request->kursendtermin);
        $kursstarttermin = Carbon::parse($request->kursstarttermin);
        // Calculate the difference in minutes
        $diffDay = $kursendterminBerechnung->diffInDays($kursstarttermin);
        $kursendterminBerechnungDatum = Carbon::parse($request->kursendtermin)->format('Y-m-d');
        $kursstartterminDatum = Carbon::parse($request->kursstarttermin)->format('Y-m-d');
        $diffMinute = $kursendterminBerechnung->diffInMinutes($kursstarttermin);
        if($kursendterminBerechnungDatum < $kursstartterminDatum)
        {
            $diffMinute = $diffMinute * -1;
        }

        if($diffMinute< $kurslaeneminuten)
         {
             // FixMe: Self::danger('... funktioniert nicht $dangeer = wurde als alternative verwendet
             //self::danger('Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.');
             $danger = 'Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.
             Der Kurs Endtermin wurde automatisch berechnet. Bitte überprüfe die Daten nochmal.';

            $courses = Course::where('sportSection_id' , env('KURS_ABTEILUNG', 1))
                ->orderByDesc('kursName')
                ->get();

            $course_id = $request->course_id;
            $sportgeraetanzahl= $request->sportgeraetanzahl;
            $kurslaenge = $request->kurslaenge;
            $sportgeraetanzahlMax = $this->sportgeraetanzahlMax();

            return view('components.backend.courseDate.create' , compact([
                'kursstarttermin',
                'kurslaenge',
                'kursendtermin',
                'sportgeraetanzahlMax',
                'sportgeraetanzahl',
                'courses',
                'danger',
                'course_id'

            ]));
        }

        $coursedate = new coursedate(
            [
                'trainer_id'         => Auth::user()->id,
                'sportSection_id'    => env('KURS_ABTEILUNG', 1),
                'course_id'          => $request->course_id,
                'kurslaenge'         => $request->kurslaenge,
                'kursstarttermin'    => $request->kursstarttermin,
                'kursendtermin'      => $kursendtermin,
                'kursstartvorschlag' => $request->kursstarttermin,
                'kursendvorschlag'   => $kursendtermin,
                'sportgeraetanzahl'  => $request->sportgeraetanzahl,
                'bearbeiter_id'      => Auth::user()->id,
                'user_id'            => Auth::user()->id,
                'updated_at'         => Carbon::now(),
                'created_at'         => Carbon::now()
            ]
        );
        $coursedate->save();

        self::success('Kurstermin wurde erfolgreich angelegt.');

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
        $date = Carbon::parse($request->kursstarttermin);
        $time = Carbon::parse($request->kurslaenge);
        // Extract the hours and minutes from the time
        $hours = $time->hour;
        $minutes = $time->minute;
        $kurslaeneminuten =  $hours*60 + $minutes;
        $kursendtermin = $date->addHours($hours)->addMinutes($minutes);

        // Parse the dates to Carbon instances
        $kursendterminBerechnung = Carbon::parse($request->kursendtermin);
        $kursstarttermin = Carbon::parse($request->kursstarttermin);
        // Calculate the difference in minutes
        $diffDay = $kursendterminBerechnung->diffInDays($kursstarttermin);
        $kursendterminBerechnungDatum = Carbon::parse($request->kursendtermin)->format('Y-m-d');
        $kursstartterminDatum = Carbon::parse($request->kursstarttermin)->format('Y-m-d');
        $diffMinute = $kursendterminBerechnung->diffInMinutes($kursstarttermin);
        if($kursendterminBerechnungDatum < $kursstartterminDatum)
        {
            $diffMinute = $diffMinute * -1;
        }

        if($diffMinute < $kurslaeneminuten)
        {
            // FixMe: Self::danger('... funktioniert nicht $dangeer = wurde als alternative verwendet
            //self::danger('Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.');
            $danger = 'Die Kurslänge ist grösser als der Zeitabstand zwischen Kurs Start- und Kurs Endtermin.
             Der Kurs Endtermin wurde automatisch berechnet. Bitte überprüfe die Daten nochmal.';

            $courses = Course::where('sportSection_id' , env('KURS_ABTEILUNG', 1))
                ->orderByDesc('kursName')
                ->get();

            $course_id = $request->course_id;
            $sportgeraetanzahl= $request->sportgeraetanzahl;
            $kurslaenge = $request->kurslaenge;

            $coursedate = $request->coursedate;

            $sportgeraetanzahlMax = $this->sportgeraetanzahlMax();

            return view('components.backend.courseDate.edit' , compact([
                'coursedate',
                'courses',
                'kursstarttermin',
                'kurslaenge',
                'kursendtermin',
                'sportgeraetanzahlMax',
                'sportgeraetanzahl',
                'danger',
                'course_id'

            ]));
        }

        $coursedate->update(
            [
                'course_id'          => $request->course_id,
                'kurslaenge'         => $request->kurslaenge,
                'kursstarttermin'    => $request->kursstarttermin,
                'kursendtermin'      => $request->kursendtermin,
                'kursstartvorschlag' => $request->kursstarttermin,
                'kursendvorschlag'   => $request->kursendtermin,
                'sportgeraetanzahl'  => $request->sportgeraetanzahl,
                'bearbeiter_id'      => Auth::user()->id,
                'updated_at'         => Carbon::now()
            ]
        );

        self::success('Kurstermin wurde erfolgreich bearbeitet.');

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
}
