<?php

namespace App\Http\Controllers\CourseBooking;

use App\Http\Controllers\Controller;
use App\Models\Coursedate;
use App\Models\CourseParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ParticipantMailController extends Controller
{
    public function participantMail()
    {
        $courseParticipants = CourseParticipant::where('teilnehmernachricht', 1)->get();

        foreach ($courseParticipants as $courseParticipant) {

            $coursedates = Coursedate::where('kursstarttermin', '>=', date('Y-m-d', strtotime('now')))
            ->join('course_participant_bookeds', 'coursedates.id', '=', 'course_participant_bookeds.kurs_id')
            ->where('course_participant_bookeds.participant_id', $courseParticipant->id)
            ->where('course_participant_bookeds.deleted_at', null)
            ->get();

            if($coursedates->count()>0) {
                Mail::to($courseParticipant->email)->send(new \App\Mail\ParticipantMail($coursedates, $courseParticipant));
                    //Temp: Zusätzlicher Testmailversand an Vereinshomepage Technik
                Mail::to(env('VEREIN_HP_TECH_VERTRETEMAIL'))->send(new \App\Mail\ParticipantMail($coursedates, $courseParticipant));
            }
       }

       CourseParticipant::where('teilnehmernachricht', '1')
            ->update(['teilnehmernachricht' => '']);

       self::success('Informationsmails wurden erfolgreich versendet.');

       return redirect()->route('admin.dashboard');

    }
}
