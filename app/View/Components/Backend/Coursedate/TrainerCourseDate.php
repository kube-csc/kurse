<?php

namespace App\View\Components\Backend\Coursedate;

use App\Models\Coursedate;
use App\Models\Organiser;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TrainerCourseDate extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $Yearnow = date('Y', strtotime('now')).'-01-01 00:00:00';

        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        $courseDateCountYou = CourseDate::join('coursedate_user', 'coursedates.id', '=', 'coursedate_user.coursedate_id')
                ->where('coursedate_user.user_id', auth()->user()->id)
                ->where('coursedates.organiser_id', $organiser->id)
                ->where('coursedates.kursendtermin', '>=' , date('Y-m-d', strtotime('now')))
                ->withoutTrashed()
                ->count();

        $courseDateCount = CourseDate::where('organiser_id', $organiser->id)
            ->where('kursendtermin', '>=' , date('Y-m-d', strtotime('now')))
            ->withoutTrashed()
            ->count();

        $courseDateCountYouAll = CourseDate::join('coursedate_user', 'coursedates.id', '=', 'coursedate_user.coursedate_id')
            ->where('coursedate_user.user_id', auth()->user()->id)
            ->where('organiser_id', $organiser->id)
            ->where('kursendtermin', '>=' , $Yearnow)
            ->withoutTrashed()
            ->count();

        $courseDateCountAll = CourseDate::where('organiser_id', $organiser->id)
            ->where('kursendtermin', '>=' , $Yearnow)
            ->withoutTrashed()
            ->count();

        return view('components.backend.courseDate.trainer-course-date', [
            'courseDateCountYou'    => $courseDateCountYou,
            'courseDateCount'       => $courseDateCount,
            'courseDateCountYouAll' => $courseDateCountYouAll,
            'courseDateCountAll'    => $courseDateCountAll
        ]);
    }
}
