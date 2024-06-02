<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\SportSection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $organiser = $this->organiser();

       $courses = Course::where('organiser_id', $organiser->id)
              ->orderBy('kursName')
              ->get();

       return view('components.backend.course.index', compact('courses'));
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
    public function store(StoreCourseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $course = Course::find($id);

        //ToDo: Verbessern der Abfrage
        $pickedSportSections = SportSection::join('course_sport_section', 'course_sport_section.sport_section_id', '=', 'sport_sections.id')
            ->where('course_sport_section.course_id', $course->id)
            ->orderBy('abteilung')
            ->get();

        $organiser = $this->organiser();

        $spotsectionOrganisers = SportSection::join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_sections.id')
            ->where('organiser_sport_section.organiser_id', $organiser->id)
            ->get();

        $sportSections = SportSection::orderBy('abteilung')->get();
        $pickedSportSectionIds = $pickedSportSections->pluck('sport_section_id');
        $sportSections = $sportSections->whereNotIn('id', $pickedSportSectionIds);
        $spotsectionOrganiserids = $spotsectionOrganisers->pluck('sport_section_id');
        $sportSections = $sportSections->whereIn('id', $spotsectionOrganiserids);

        return view('components.backend.course.edit', compact('course', 'sportSections', 'pickedSportSections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        //ToDo: Verbessern der Validierung
        //$data = $request->validated();

        $data = $request->validate([
            'kursName'         => 'required',
            'kursBeschreibung' => 'nullable',
            'trainer'          => 'in:0,1'
        ]);

        if(!isset($data['trainer'])){
            $data['trainer'] = 0;
        }
        $data['bearbeiter_id'] = Auth::user()->id;
        $data['updated_at'] = Carbon::now();

        $course->update($data);

        self::success('Die Kursdaten wurden erfolgreich geändert.');

        return redirect()->route('backend.course.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }

    public function pickSportSection($courseId, $pickSportSectionId)
    {
        $course = Course::find($courseId);
        $course->sportSection()->attach($pickSportSectionId);

        self::success('Sportart wurde erfolgreich zugeordnet.');

        return redirect()->route('backend.course.edit', $courseId);
    }

    public function destroySportSection($courseId, $destroySportSectionId)
    {
        $course = Course::find($courseId);
        $course->sportSection()->detach($destroySportSectionId);

        self::success('Sportart wurde erfolgreich entfernt.');

        return redirect()->route('backend.course.edit', $courseId);
    }
}
