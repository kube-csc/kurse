<?php

namespace App\View\Components\backend\Coursedate;

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
        $courseDateCount = \App\Models\CourseDate::where('trainer_id', auth()->user()->id)->count();

        return view('components.backend.coursedate.trainer-course-date', compact('courseDateCount'));
    }
}
