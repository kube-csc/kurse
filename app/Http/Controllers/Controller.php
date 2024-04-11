<?php

namespace App\Http\Controllers;

use App\Components\FlashMessages;
use App\Models\Coursedate;
use App\Models\Organiser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    use FlashMessages;

    public function organiser()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            $organiser = Organiser::find(1);
        }

        return $organiser;
    }

    public function organiserDomainId()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            $organiser = Organiser::find(1);
        }

        return $organiser->id;
    }

    public function kursendtermin($request, $coursedate)
    {
        $date             = Carbon::parse($coursedate->kursstarttermin)->format('Y-m-d');
        $kursstarttermin  = Carbon::parse($date.' '.$request->kursstartterminTime);
        $time             = Carbon::parse($coursedate->kurslaenge);
        $hours            = $time->hour;
        $minutes          = $time->minute;
        $kursendtermin    = Carbon::parse($date.' '.$request->kursstartterminTime)->addHours($hours)->addMinutes($minutes);

        return [
            'kursstarttermin' => $kursstarttermin,
            'kursendtermin'   => $kursendtermin,
        ];
    }

    public function kursendterminMin($request, $message)
    {
        $date                  = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime);
        $time                  = Carbon::parse($request->kurslaenge);

        $hours                 = $time->hour;
        $minutes               = $time->minute;
        $kurslaeneminuten      = $hours*60 + $minutes;
        $kursenddatum          = Carbon::parse($request->kursendterminDatum.' '.$request->kursendterminTime);
        $diffMinute            = $date->diffInMinutes($kursenddatum);

        if($kursenddatum < $date) {
            $diffMinute = $diffMinute * -1;
        }

        if($diffMinute < $kurslaeneminuten) {
            $kursendtermin = Carbon::parse($request->kursstartterminDatum.' '.$request->kursstartterminTime)->addHours($hours)->addMinutes($minutes);
            $message=$message[2];
        }
        else {
            $kursendtermin = $request->kursendterminDatum.' '.$request->kursendterminTime;
            $message=$message[1];
        }

        return [
            'kursstarttermin' => $date,
            'kursendtermin'   => $kursendtermin,
            'message'         => $message,
        ];
    }

    public function timeOptimizationTrainer($cousedateId)
    {
        $coursedate = Coursedate::find($cousedateId);
        $trainers = \DB::table('coursedate_user')
            ->where('coursedate_id', $cousedateId)
            ->get();

        // Einkürzen von Terminen die im Zeitraum des Kurses liegen. Dieses wird realisiert indem die Startzeit auf die Endzeit des gebuchten Termins gesetzt wird.
        foreach ($trainers as $trainer) {
            $coursedateOptimizationAlls = Coursedate::join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
                ->where('coursedates.organiser_id', $this->organiserDomainId())
                ->where('coursedates.id', '<>', $cousedateId)
                ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
                ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
                ->where('coursedates.kursendtermin', '>', $coursedate->kursstarttermin)
                ->where('coursedate_user.user_id', $trainer->user_id)
                ->orderBy('coursedate_user.coursedate_id')
                ->orderBy('coursedates.kursstarttermin')
                ->get();

            $coursedateOptimizationBoockeds = Coursedate::join('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
                ->where('coursedates.organiser_id', $this->organiserDomainId())
                ->where('coursedates.id', '<>', $cousedateId)
                ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
                ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
                ->where('coursedates.kursendtermin', '>', $coursedate->kursstarttermin)
                ->get();

            $coursedateOptimizationBoockedsIds = $coursedateOptimizationBoockeds->pluck('kurs_id');
            $coursedateOptimizations = $coursedateOptimizationAlls->whereNotIn('id', $coursedateOptimizationBoockedsIds);

            foreach ($coursedateOptimizations as $coursedateOptimization) {
                $counter = 0;
                $time = Carbon::parse($coursedateOptimization->kurslaenge);
                $hours = $time->hour;
                $minutes = $time->minute;
                $kurslaeneminuten = $hours * 60 + $minutes;

                $timeControl = Carbon::parse($coursedate->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursstarttermin));
                if ($timeControl <= $kurslaeneminuten * 2.1) {
                    $counter = 1;
                    $diffMinute = Carbon::parse($coursedate->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendtermin));
                    $update = Coursedate::find($coursedateOptimization->coursedate_id);
                    if ($kurslaeneminuten < $diffMinute) {
                        $update->update(['kursstarttermin' => $coursedate->kursendtermin]);
                    } else {
                        $update->update([
                            'kursstarttermin' => $coursedate->kursendtermin,
                            'kursNichtDurchfuerbar' => true
                        ]);
                    }
                }

                $timeControl = Carbon::parse($coursedate->kursstarttermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendtermin));
                if ($timeControl <= $kurslaeneminuten * 2.1) {
                    $counter = 1;
                    $diffMinute = Carbon::parse($coursedate->kursstarttermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursstarttermin));
                    $update = Coursedate::find($coursedateOptimization->coursedate_id);
                    if ($kurslaeneminuten <= $diffMinute) {
                        $update->update(['kursendtermin' => $coursedate->kursstarttermin]);
                    } else {
                        $update->update([
                            'kursendtermin' => $coursedate->kursstarttermin,
                            'kursNichtDurchfuerbar' => true
                        ]);
                    }
                }

            }

            if($counter == 0) {
                $coursedateOptimizationAlls = Coursedate::join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
                    ->where('coursedates.organiser_id', $this->organiserDomainId())
                    ->where('coursedates.id', '<>', $cousedateId)
                    ->where('coursedates.kursstarttermin', '<=', $coursedate->kursendtermin)
                    ->where('coursedates.kursendtermin', '>=', $coursedate->kursstarttermin)
                    ->where('coursedates.kursendtermin', '>', $coursedate->kursstarttermin)
                    ->where('coursedate_user.user_id', $trainer->user_id)
                    ->orderBy('coursedate_user.coursedate_id')
                    ->orderBy('coursedates.kursstarttermin')
                    ->get();

                $coursedateOptimizationBoockedsIds = $coursedateOptimizationBoockeds->pluck('kurs_id');
                $coursedateOptimizations = $coursedateOptimizationAlls->whereNotIn('id', $coursedateOptimizationBoockedsIds);

                foreach ($coursedateOptimizations as $coursedateOptimization) {
                    $time = Carbon::parse($coursedateOptimization->kurslaenge);
                    $hours = $time->hour;
                    $minutes = $time->minute;
                    $kurslaeneminuten = $hours * 60 + $minutes;

                    if ($counter == 1) {
                        $diffMinute = Carbon::parse($coursedate->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendtermin));
                        $update = Coursedate::find($coursedateOptimization->coursedate_id);
                        if ($kurslaeneminuten < $diffMinute) {
                            $update->update(['kursstarttermin' => $coursedate->kursendtermin]);
                        }
                    }

                    if ($counter == 0 and $coursedateOptimizations->count() > 1) {
                        $diffMinute = Carbon::parse($coursedate->kursstarttermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursstarttermin));
                        if ($kurslaeneminuten <= $diffMinute) {
                            $counter = 1;
                            $update = Coursedate::find($coursedateOptimization->coursedate_id);
                            $update->update(['kursendtermin' => $coursedate->kursstarttermin]);
                        }
                    }

                }
            }

            return $coursedateOptimizations;
        }
    }

}
