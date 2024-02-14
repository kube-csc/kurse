<?php

namespace App\View\Components\Backend\Coursedate;

use App\Models\Coursedate;
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

        $courseDateCountYou = CourseDate::where('trainer_id', auth()->user()->id)
                ->where('sportSection_id', env('KURS_ABTEILUNG' , 1))
                ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
                ->withoutTrashed()
                ->count();

        $courseDateCount = CourseDate::where('sportSection_id', env('KURS_ABTEILUNG' , 1))
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->withoutTrashed()
            ->count();

        $courseDateCountYouAll = CourseDate::where('trainer_id', auth()->user()->id)
            ->where('sportSection_id', env('KURS_ABTEILUNG' , 1 ))
            ->where('kursstarttermin', '>=' , $Yearnow)
            ->withoutTrashed()
            ->count();

        $courseDateCountAll = CourseDate::where('sportSection_id', env('KURS_ABTEILUNG', 1 ))
            ->where('kursstarttermin', '>=' , $Yearnow)
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
