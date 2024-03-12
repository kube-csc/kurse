<?php

namespace App\Http\Controllers;

use App\Models\CourseParticipantBooked;
use App\Models\Organiser;
use App\Models\Trainertable;
use App\Models\Coursedate;
use App\Models\Course;
use App\Models\SportEquipment;
use Illuminate\Support\Carbon;

// ToDo: Wird es noch benötigt?
// use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $yearnow = date('Y', strtotime('now')).'-01-01 00:00:00';

        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        $coursedates = Coursedate::where('organiser_id', $organiser->id)
                                 ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
                                 ->orderBy('kursendtermin')
                                 ->get();

        $yearAgo = Carbon::now()->subDays(365);
        $courses = Course::select('courses.kursName')
            ->join('coursedates', 'courses.id', '=', 'coursedates.course_id')
            ->where('coursedates.kursendtermin', '>=' , $yearAgo)
            ->where('courses.organiser_id', $organiser->id)
            ->groupBy('courses.kursName')
            ->get();

        $sportEquipments = SportEquipment::join('organiser_sport_section', 'sport_equipment.sportSection_id', '=', 'organiser_sport_section.sport_section_id')
            ->join ('organisers', 'organiser_sport_section.organiser_id', '=', 'organisers.id')
            ->where('organisers.id', $organiser->id)
            ->get();

        $courseDateCountAll = CourseDate::where('organiser_id', $organiser->id)
            ->where('kursendtermin', '>=' , $yearnow)
            ->withoutTrashed()
            ->count();

        $teilnehmerKursBookeds = CourseParticipantBooked::join('coursedates', 'coursedates.id', '=', 'course_participant_bookeds.kurs_id')
            ->where('coursedates.organiser_id', $organiser->id)
            ->where('course_participant_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '>=', $yearnow)
            ->where('coursedates.kursendtermin', '<=' , date('Y-m-d', strtotime('now')))
            ->get()->count();

        $teilnehmerKursBookedNows = CourseParticipantBooked::join('coursedates', 'coursedates.id', '=', 'course_participant_bookeds.kurs_id')
            ->where('course_participant_bookeds.deleted_at', null)
            ->where('coursedates.kursendtermin', '>=', $yearnow)
            ->where('coursedates.kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->get()->count();

        $trainers = Trainertable::join('organiser_sport_section', 'trainertables.sportSection_id', '=', 'organiser_sport_section.sport_section_id')
            ->join ('organisers', 'organiser_sport_section.organiser_id', '=', 'organisers.id')
            ->join('trainertyps', 'trainertables.trainertyp_id', '=', 'trainertyps.id')
            ->where('trainertyps.status', 1)
            ->where('organisers.id', $organiser->id)
            ->where('trainertables.organiser_id', $organiser->id)
            ->where('trainertables.sichtbar', 1)
            ->where('trainertables.status', 1)
            ->get();

        return view('pages.home' , [
                    'trainers'                  => $trainers,
                    'countTrainers'             => $trainers->count(),
                    'coursedates'               => $coursedates,
                    'countCoursedates'          => $coursedates->count(),
                    'courses'                   => $courses,
                    'sportEquipments'           => $sportEquipments,
                    'courseDateCountAll'        => $courseDateCountAll,
                    'teilnehmerKursBookeds'     => $teilnehmerKursBookeds,
                    'teilnehmerKursBookedNows'  => $teilnehmerKursBookedNows,
                    'organiser'                 => $organiser
        ]);
    }

    public function offer()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        $coursdates = Coursedate::where('organiser_id', $organiser->id)
            ->where('coursedates.kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->orderBy('coursedates.kursstarttermin')
            ->get();

        return view('pages.offer' , [
            'organiser'          => $organiser,
            'countCoursdates'    => $coursdates->count(),
        ]);
    }

    public function sportType()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            $organiser = Organiser::find(1);
        }

        return view('pages.sportType')->with('organiser', $organiser);
    }

    public function trainer()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            $organiser = Organiser::find(1);
        }

        $trainers = Trainertable::join('organiser_sport_section', 'trainertables.sportSection_id', '=', 'organiser_sport_section.sport_section_id')
            ->join ('organisers', 'organiser_sport_section.organiser_id', '=', 'organisers.id')
            ->join('trainertyps', 'trainertables.trainertyp_id', '=', 'trainertyps.id')
            ->where('trainertyps.status', 1)
            ->where('organisers.id', $organiser->id)
            ->where('trainertables.organiser_id', $organiser->id)
            ->where('trainertables.sichtbar', 1)
            ->where('trainertables.status', 1)
            ->get();

        return view('pages.trainer'  , [
            'trainers'        => $trainers,
            'countTrainers'   => $trainers->count(),
            'organiser'       => $organiser
        ]);
    }

    public function sportUnit()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            $organiser = Organiser::find(1);
        }

        //ToDo: Muss noch angepasst werden
        $sportEquipments = SportEquipment::
                  join('organiser_sport_section', 'sport_equipment.sportSection_id', '=', 'organiser_sport_section.sport_section_id')
                ->join ('organisers', 'organiser_sport_section.organiser_id', '=', 'organisers.id')
                ->where('organisers.id', $organiser->id)
                ->get();

        return view('pages.sportUnit' , [
            'countSportEquipments'               => $sportEquipments->count(),
            'sportEquipments'                    => $sportEquipments,
            'organiser'                           => $organiser,
        ]);
    }

    public function courseType()
    {
        $organiserDomainId=$this->organiserDomainId();

        $courseAlls  = Course::where('organiser_id', $organiserDomainId)->get();
        $yearAgo = Carbon::now()->subDays(365);
        $coursesYearago = Course::select('courses.id')
            ->join('coursedates', 'courses.id', '=', 'coursedates.course_id')
            ->where('coursedates.kursendtermin', '>=' , $yearAgo)
            ->where('courses.organiser_id', $organiserDomainId)
            ->groupBy('courses.id')
            ->get();
        $courses = $courseAlls->intersect($coursesYearago)->unique('id');

        return view('pages.course' , [
            'courses' => $courses
        ]);
    }

    public function courseDate($id)
    {
        $coursedate = Coursedate::find($id);

        return view('pages.coursedate' , [
            'coursedate' => $coursedate
        ]);
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
}
