<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Coursedate;
use App\Models\Trainertable;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class TrainerMailController extends Controller
{
    public function trainerMail()
    {
        $trainers = Trainertable::join('users', 'trainertables.user_id', '=', 'users.id')
            ->where('users.trainernachricht', 1)->get();

        foreach ($trainers as $trainer) {

            $coursedate = Coursedate::where('kursstarttermin', '>=', date('Y-m-d', strtotime('now')))
                ->join('coursedate_user', 'coursedates.id', '=', 'coursedate_user.coursedate_id')
                ->where('coursedate_user.user_id', $trainer->user_id)
                ->orderBy('kursstarttermin')
                ->first();

            if ($coursedate) {
                $kursstarttermin = new Carbon($coursedate->kursstarttermin);
                $now = Carbon::now();

                if ($now->diffInDays($kursstarttermin, false) < 3) {
                    $coursedates = Coursedate::where('kursstarttermin', '>=', date('Y-m-d', strtotime('now')))
                        ->where('coursedate_user.user_id', $trainer->user_id)
                        ->join('coursedate_user', 'coursedates.id', '=', 'coursedate_user.coursedate_id')
                        ->orderBy('kursstarttermin')
                        ->get();

                    if($coursedates->count()>0) {
                        Mail::to($trainer->getKursTrainer->email)->send(new \App\Mail\TrainerMail($coursedates, $trainer));
                        //Temp: Zusätzlicher Testmailversand an Vereinshomepage Technik
                        Mail::to(env('VEREIN_HP_TECH_VERTRETEMAIL'))->send(new \App\Mail\TrainerMail($coursedates, $trainer));
                    }
                }
            }

            User::where('trainernachricht', '1')
                ->update(['trainernachricht' => '']);

            self::success('Informationsmails wurden erfolgreich versendet.');

            return redirect()->route('admin.dashboard');
        }
    }
}
