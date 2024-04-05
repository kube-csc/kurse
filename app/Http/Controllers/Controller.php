<?php

namespace App\Http\Controllers;

use App\Components\FlashMessages;
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

}
