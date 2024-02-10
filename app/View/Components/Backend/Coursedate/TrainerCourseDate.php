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
        $courseDateCount = CourseDate::where('trainer_id', auth()->user()->id)
                ->where('sportSection_id', env('KURS_ABTEILUNG'))
                ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
                ->count();

        $courseDateCountAll = CourseDate::where('trainer_id', auth()->user()->id)
            ->where('sportSection_id', env('KURS_ABTEILUNG'))
            ->count();

        return view('components.backend.courseDate.trainer-course-date', [
            'courseDateCount' => $courseDateCount,
            'courseDateCountAll' => $courseDateCountAll
        ]);
    }
}
