<?php

namespace App\Http\Controllers;

use App\Models\SportEquipmentBooked;
use App\Models\Trainertable;
use App\Models\Coursedate;
use App\Models\Course;
use App\Models\SportEquipment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trainers   = Trainertable::where('sportSection_id', env('KURS_ABTEILUNG',1))->get();

        $coursdates = Coursedate::where('sportSection_id', env('KURS_ABTEILUNG',1))
                                ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
                                ->orderBy('kursendtermin')
                                ->get();

        $courses = Course::where('sportSection_id', env('KURS_ABTEILUNG',1))->get();

        $sportEquipments = SportEquipment::where('sportSection_id', env('KURS_ABTEILUNG',1))->get();

        $yearnow = date('Y', strtotime('now')).'-01-01 00:00:00';
        $courseDateCountAll = CourseDate::where('sportSection_id', env('KURS_ABTEILUNG', 1 ))
            ->where('kursendtermin', '>=' , $yearnow)
            ->withoutTrashed()
            ->count();

        $teilnehmerKursBookeds = SportEquipmentBooked::
              join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->where('sport_equipment_bookeds.trainer_id', '<>', 0)
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '>=', $yearnow)
            ->where('coursedates.kursendtermin', '=<' , date('Y-m-d', strtotime('now')))
            ->get()->count();

        $teilnehmerKursBookedNows = SportEquipmentBooked::
               join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->where('sport_equipment_bookeds.trainer_id', '<>', 0)
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('coursedates.kursendtermin', '>=', $yearnow)
            ->where('coursedates.kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->get()->count();

        return view('pages.home' , [
                    'trainers'                  => $trainers,
                    'countTrainers'             => $trainers->count(),
                    'coursdates'                => $coursdates,
                    'countCoursdates'           => $coursdates->count(),
                    'courses'                   => $courses,
                    'sportEquipments'           => $sportEquipments,
                    'courseDateCountAll'        => $courseDateCountAll,
                    'teilnehmerKursBookeds'     => $teilnehmerKursBookeds,
                    'teilnehmerKursBookedNows'  => $teilnehmerKursBookedNows
        ]);
    }

    public function offer()
    {
        $coursdates = Coursedate::where('sportSection_id', env('KURS_ABTEILUNG',1))
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->get();

        return view('pages.offer' , [
            'countCoursdates' => $coursdates->count(),
        ]);
    }

    public function sportType()
    {
        return view('pages.sportType' );
    }

    public function trainer()
    {
        $trainers = Trainertable::where('sportSection_id', env('KURS_ABTEILUNG',1))->get();

        return view('pages.trainer'  , [
            'trainers'        => $trainers,
            'countTrainers'   => $trainers->count(),
        ]);
    }

    public function sportUnit()
    {
        $sportEquipments = SportEquipment::where('sportSection_id', env('KURS_ABTEILUNG',1))->get();

        return view('pages.sportUnit' , [
            'countSportEquipments' => $sportEquipments->count(),
            'sportEquipments' => $sportEquipments,
        ]);
    }

    public function courseType()
    {
        $courses    = Course::where('sportSection_id', env('KURS_ABTEILUNG',1))->get();

        return view('pages.course' , [
            'courses'         => $courses
        ]);
    }

    public function courseDate($id)
    {
        $coursedate = Coursedate::find($id);

        return view('pages.coursedate' , [
            'coursedate' => $coursedate
        ]);
    }

}
