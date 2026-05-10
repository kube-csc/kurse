<?php

namespace App\View\Components\CourseBooking;

use App\Models\Coursedate;
use App\Models\Organiser;
use Auth;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dashboard extends Component
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
        $organiserId = $this->organiserDomain()->id;

        $courseIdsParam = session('course_embed_filter');
        $filterCourseIds = [];
        if ($courseIdsParam !== null) {
            if (is_array($courseIdsParam)) {
                $filterCourseIds = array_map('intval', $courseIdsParam);
            } else {
                $filterCourseIds = array_map('intval', explode(',', $courseIdsParam));
            }
            $filterCourseIds = array_filter($filterCourseIds);
        }

        $courseDateCountQuery = Coursedate::where('coursedates.organiser_id', $organiserId)
            // ToDo:Vorher Filter so das nur noch Ergebnisse vorhanden sind die den angemeldeten Trainer zugeordnet sind
            ->where('kursstarttermin', '>=', date('Y-m-d', strtotime('now')));

        if (!empty($filterCourseIds)) {
            $courseDateCountQuery->whereIn('coursedates.course_id', $filterCourseIds);
        }
        $courseDateCount = $courseDateCountQuery->count();

        $courseDateCountYouQuery = Coursedate::where('coursedates.organiser_id', $organiserId)
            ->join('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
            ->where('participant_id', Auth::user()->id)
            ->where('kursstarttermin', '>=', date('Y-m-d', strtotime('now')))
            ->whereNull('course_participant_bookeds.deleted_at');

        if (!empty($filterCourseIds)) {
            $courseDateCountYouQuery->whereIn('coursedates.course_id', $filterCourseIds);
        }
        $courseDateCountYou = $courseDateCountYouQuery->distinct('coursedates.id')->count();

        $courseParticipantCountQuery = Coursedate::where('coursedates.organiser_id', $organiserId)
            ->join('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
            ->where('participant_id', Auth::user()->id)
            ->where('kursstarttermin', '>=', date('Y-m-d', strtotime('now')))
            ->whereNull('course_participant_bookeds.deleted_at');

        if (!empty($filterCourseIds)) {
            $courseParticipantCountQuery->whereIn('coursedates.course_id', $filterCourseIds);
        }
        $courseParticipantCount = $courseParticipantCountQuery->count();

        return view('components.courseBooking.dashboard', [
            'courseDateCountYou'     => $courseDateCountYou,
            'courseDateCount'        => $courseDateCount,
            'courseParticipantCount' => $courseParticipantCount,
        ]);
    }

    public function organiserDomain()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            $organiser = Organiser::find(1);
        }

        return $organiser;
    }
}
