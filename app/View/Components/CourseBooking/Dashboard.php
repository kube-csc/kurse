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

        $courseDateCount = Coursedate::where('coursedates.organiser_id', $organiserId)
            // ToDo:Vorher Filter so das nur noch Ergebnisse vorhanden sind die den angemeldeten Trainer zugeordnet sind
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->count();

        $courseDateCountYou = Coursedate::where('coursedates.organiser_id', $organiserId)
            ->join('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
            ->where('participant_id', Auth::user()->id)
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->whereNull('course_participant_bookeds.deleted_at')
            ->distinct('coursedates.id')
            ->count();

        $courseParticipantCount = Coursedate::where('coursedates.organiser_id', $organiserId)
            ->join('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
            ->where('participant_id', Auth::user()->id)
            ->where('kursstarttermin', '>=' , date('Y-m-d', strtotime('now')))
            ->whereNull('course_participant_bookeds.deleted_at')
            ->count();

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
