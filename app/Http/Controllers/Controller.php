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

    public function testBookCount($cousedateId)
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
                ->where('coursedates.kursstartvorschlag', '<=', $coursedate->kursendvorschlag)
                ->where('coursedates.kursendvorschlag', '>=', $coursedate->kursstartvorschlag)
                ->where('coursedate_user.user_id', $trainer->user_id)
                ->orderBy('coursedate_user.coursedate_id')
                ->orderBy('coursedates.kursstarttermin')
                ->get();

            $coursedateOptimizationBoockeds = Coursedate::join('course_participant_bookeds', 'course_participant_bookeds.kurs_id', '=', 'coursedates.id')
                ->where('coursedates.organiser_id', $this->organiserDomainId())
                ->where('coursedates.kursstartvorschlag', '<=', $coursedate->kursendvorschlag)
                ->where('coursedates.kursendvorschlag', '>=', $coursedate->kursstartvorschlag)
                ->orderBy('coursedates.kursstarttermin')
                ->get();

            $coursedateOptimizationBoockedsIds = $coursedateOptimizationBoockeds->pluck('kurs_id');
            $coursedateOptimizations = $coursedateOptimizationAlls->whereNotIn('coursedate_id', $coursedateOptimizationBoockedsIds);

            if($coursedateOptimizationBoockedsIds->count()<0) {
                $this->timeOptimizationTrainerFirst($coursedate->id, $coursedate, $coursedateOptimizationAlls, $coursedateOptimizationBoockeds, $coursedateOptimizations, $trainer);
            }
            else {
                $this->timeOptimizationTrainer($coursedate->id, $coursedate, $coursedateOptimizationAlls, $coursedateOptimizationBoockeds, $coursedateOptimizations, $trainer);
            }
        }
    }

    public function timeOptimizationTrainerFirst($cousedateId, $coursedate, $coursedateOptimizationAlls, $coursedateOptimizations, $trainer)
    {
       foreach ($coursedateOptimizations as $coursedateOptimization) {
            $startus = 0;
            $kurslaeneminuten = $this->kurslaenge($coursedateOptimization->kurslaenge);

            //Erstmöglicher Termin
            $timeControl = Carbon::parse($coursedate->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursstarttermin));
            if ($timeControl <= $kurslaeneminuten * 2.1) {
                $startus = 1;
                $diffMinute = Carbon::parse($coursedate->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendtermin));
                $update = Coursedate::find($coursedateOptimization->coursedate_id);
                if ($kurslaeneminuten < $diffMinute) {
                    $update->update(['kursstarttermin' => $coursedate->kursendtermin]);
                    self::success('Termin wurde nach oben verschoben.'.$coursedateOptimization->coursedate_id);
                } else {
                    $update->update([
                        'kursstarttermin' => $coursedate->kursendtermin,
                        'kursNichtDurchfuerbar' => true
                    ]);
                    self::success('Termin wurde nach oben blind verschoben.'.$coursedateOptimization->coursedate_id);
                    continue;
                }
            }

            //Letzmöglicher Termin
            $timeControl = Carbon::parse($coursedate->kursstarttermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendtermin));
            if ($timeControl <= $kurslaeneminuten * 2.1) {
                $startus = 1;
                $diffMinute = Carbon::parse($coursedate->kursstarttermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursstarttermin));
                $update = Coursedate::find($coursedateOptimization->coursedate_id);
                if ($kurslaeneminuten <= $diffMinute) {
                    $update->update(['kursendtermin' => $coursedate->kursstarttermin]);
                    self::success('Termin wurde zum ende verschoben.'.$coursedateOptimization->coursedate_id);
                } else {
                    $update->update([
                        'kursendtermin' => $coursedate->kursstarttermin,
                        'kursNichtDurchfuerbar' => true
                    ]);
                    self::success('Termin wurde zum ende blind verschoben.'.$coursedateOptimization->coursedate_id);
                }
            }
       }

       if($startus == 0) {
            foreach ($coursedateOptimizations as $coursedateOptimization) {
                $kurslaeneminuten = $this->kurslaenge($coursedateOptimization->kurslaenge);

                if ($startus == 1) {
                    $diffMinute = Carbon::parse($coursedate->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendtermin));
                    $update = Coursedate::find($coursedateOptimization->coursedate_id);
                    if ($kurslaeneminuten < $diffMinute) {
                        $update->update(['kursstarttermin' => $coursedate->kursendtermin]);
                        self::success('Termin wurde oben angehangen. '.$coursedateOptimization->coursedate_id);
                    }
                }

                if ($startus == 0 and $coursedateOptimizations->count() > 1) {
                    $diffMinute = Carbon::parse($coursedate->kursstarttermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursstarttermin));
                    if ($kurslaeneminuten <= $diffMinute) {
                        $startus = 1;
                        $update = Coursedate::find($coursedateOptimization->coursedate_id);
                        $update->update(['kursendtermin' => $coursedate->kursstarttermin]);
                        self::success('Termin wurde unten angehangen. '.$coursedateOptimization->coursedate_id);
                    }
                }

            }
       }
      // Temp:
       self::warning('First Book');
       return $coursedateOptimizations;
    }

    public function timeOptimizationTrainer($cousedateId, $coursedate, $coursedateOptimizationAlls, $coursedateOptimizationBoockeds, $coursedateOptimizations, $trainer)
    {
        //dump('coursedateOptimizations');
        //dump($coursedateOptimizations);
        //dump($coursedateOptimizations->count());
            $statusFirst=0;
            $loopCounterMax=0;
            $coursedateOptimizationBoockedDell=0;
            foreach ($coursedateOptimizations as $coursedateOptimization) {
             //dump('Neuplanung von Termin coursedateOptimization');
             //dump($coursedateOptimization);
                // Termin verschieben die nur einen freien Termin im Zeitfenster gebuchten Termin
                if($coursedateOptimizations->count()==1){
                    $kurslaeneminuten = $this->kurslaenge($coursedateOptimization->kurslaenge);
                    $loopCounter=0;
                    foreach ($coursedateOptimizationBoockeds as $coursedateOptimizationBoocked) {
                        $loopCounter++;
                        if ($loopCounter==1) {
                            //dump('erster');
                            $diffMinute = Carbon::parse($coursedateOptimization->kursstartvorschlag)->diffInMinutes(Carbon::parse($coursedateOptimizationBoocked->kursstarttermin));
                            if ($kurslaeneminuten <= $diffMinute) {
                                //dump('erster Termin');
                                $fruesterStarttermin = $coursedateOptimization->kursstartvorschlag;
                                $spaetesterEndtermin = $coursedateOptimizationBoocked->kursstarttermin;
                            }
                            $naesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                        }
                        if($loopCounter>1 and $coursedateOptimizationBoockeds->count()>$loopCounter) {
                            //dump('zweiter');
                            $diffMinute = Carbon::parse($coursedateOptimizationBoocked->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimizationBoocked->kursstarttermin));
                            if ($kurslaeneminuten <= $diffMinute) {
                                if(!isset($fruesterStarttermin)) {
                                    //dump('zweiter Termin');
                                    $fruesterStarttermin = $coursedateOptimization->kursstartvorschlag;
                                }
                                $spaetesterEndtermin = $coursedateOptimizationBoocked->kursstarttermin;
                            }
                            $naesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                        }
                        if($coursedateOptimizationBoockeds->count()==$loopCounter) {
                            //dump('letzter');
                            $diffMinute = Carbon::parse($coursedateOptimizationBoocked->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendvorschlag));
                            if ($kurslaeneminuten <= $diffMinute) {
                                if(!isset($fruesterStarttermin)) {
                                    $fruesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                                    //dump('letzter Termin');
                                }
                                $spaetesterEndtermin = $coursedateOptimizationBoocked->kursendvorschlag;
                            }
                        }
                    }
                    //dump('fruesterStarttermin '.$fruesterStarttermin);
                    //dump('spaetesterEndtermin '.$spaetesterEndtermin);
                    if(isset($fruesterStarttermin) and isset($spaetesterEndtermin)) {
                        $update = Coursedate::find($coursedateOptimization->coursedate_id);
                        $update->update([
                            'kursstarttermin' => $fruesterStarttermin,
                            'kursendtermin'   => $spaetesterEndtermin,
                            'kursNichtDurchfuerbar' => false
                        ]);
                    }
                    else
                    {
                        $update->update([
                            'kursNichtDurchfuerbar' => true
                        ]);
                    }
                }
                else {
                    //Termin verschieben die mehr als einen freien Termin im Zeitfenster gebuchten Termin
                    $loopCounter=0;
                    $kurslaeneminuten = $this->kurslaenge($coursedateOptimization->kurslaenge);
                    if($coursedateOptimizationBoockedDell>0){
                        //dump('gelöscht ');
                        //dump($coursedateOptimizationBoockeds);
                        $coursedateOptimizationBoockeds = $coursedateOptimizationBoockeds->whereNotIn('id', $coursedateOptimizationBoockedDell);
                        $coursedateOptimizationBoockedDell=0;
                        //dump($coursedateOptimizationBoockeds);
                    }
                    //dump('anzahl CoursedateOptimizationBoockeds '.$coursedateOptimizationBoockeds->count());
                    foreach ($coursedateOptimizationBoockeds as $coursedateOptimizationBoocked) {
                        $loopCounter++;
                        //dump('loopCounter '.$loopCounter);
                         //dump('Bookedcount '.$coursedateOptimizationBoockeds->count().'Loop '.$loopCounter);
                        if($loopCounter==1){
                            if ($statusFirst==0) {
                                $statusFirst = 1;
                                $fruesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                                $diffMinute = Carbon::parse($coursedateOptimization->kursstartvorschlag)->diffInMinutes(Carbon::parse($coursedateOptimizationBoocked->kursstarttermin));
                                if ($kurslaeneminuten <= $diffMinute) {
                                    $update = Coursedate::find($coursedateOptimization->coursedate_id);
                                    $update->update([
                                        'kursstarttermin'       => $coursedateOptimization->kursstartvorschlag,
                                        'kursendtermin'         => $coursedateOptimizationBoocked->kursstarttermin,
                                        'kursNichtDurchfuerbar' => false
                                    ]);
                                    //dump($coursedateOptimizationBoocked);
                                    //dump($coursedateOptimization);
                                    //dump('update first');
                                    //dump($update);
                                    break;
                                  }
                           }
                           $coursedateOptimizationBoockedDell=$coursedateOptimizationBoocked->id;
                        }
                         //dump('loopcounter '.$loopCounter.' Loopmax '. $loopCounterMax);
                        if($loopCounter>1) {
                            $diffMinute = Carbon::parse($fruesterStarttermin)->diffInMinutes(Carbon::parse($coursedateOptimizationBoocked->kursstarttermin));
                            if($fruesterStarttermin>$coursedateOptimizationBoocked->kursstarttermin){
                                $diffMinute = $diffMinute * -1;
                            }
                             //dump($coursedateOptimizationBoocked);
                             //dump($fruesterStarttermin, $coursedateOptimizationBoocked->kursstarttermin, $diffMinute);
                            if ($kurslaeneminuten <= $diffMinute) {
                                $update = Coursedate::find($coursedateOptimization->coursedate_id);
                                if($coursedateOptimizationBoockeds->count()==1) {
                                    $diffMinute = Carbon::parse($coursedateOptimizationBoocked->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendvorschlag));
                                     //dump($coursedateOptimizationBoocked->kursendtermin, $coursedateOptimization->kursendvorschlag, $diffMinute);
                                    //dump('update middel kursendvorschlag'. $coursedateOptimization->coursedate_id);
                                    $update->update([
                                        'kursstarttermin' => $fruesterStarttermin,
                                        'kursendtermin' => $coursedateOptimization->kursendvorschlag,
                                        'kursNichtDurchfuerbar' => false
                                    ]);
                                }
                                else {
                                    $update->update([
                                        'kursstarttermin' => $fruesterStarttermin,
                                        'kursendtermin' => $coursedateOptimizationBoocked->kursstarttermin,
                                        'kursNichtDurchfuerbar' => false
                                    ]);
                                    //dump('update middel kursstarttermin'. $coursedateOptimization->coursedate_id);
                                }

                                $fruesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                                 //dump('fruesterStarttermin '.$fruesterStarttermin);
                                break;
                            }
                            $fruesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                             //dump('fruesterStarttermin ohne update'.$fruesterStarttermin);
                        }

                        if($coursedateOptimizationBoockeds->count()==$loopCounter){
                            $diffMinute = Carbon::parse($coursedateOptimizationBoocked->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendvorschlag));
                             //dump($coursedateOptimizationBoocked->kursendtermin, $coursedateOptimization->kursendvorschlag, $diffMinute);
                            $update = Coursedate::find($coursedateOptimization->coursedate_id);
                            if ($kurslaeneminuten <= $diffMinute) {
                                $update->update([
                                    'kursstarttermin'       => $coursedateOptimizationBoocked->kursendtermin,
                                    'kursendtermin'         => $coursedateOptimization->kursendvorschlag,
                                    'kursNichtDurchfuerbar' => false
                                ]);
                                //dump('coursedateOptimizationBoocked ');
                                //dump($coursedateOptimizationBoocked);
                                //dump('Last update false '.$coursedateOptimization->coursedate_id. 'Daten von coursedateOptimizationBoocked '.$coursedateOptimizationBoocked->coursedate_id);
                            }
                            else{
                                $update->update([
                                    'kursstarttermin'       => $coursedateOptimizationBoocked->kursendtermin,
                                    'kursendtermin'         => $coursedateOptimization->kursendvorschlag,
                                    'kursNichtDurchfuerbar' => true
                                ]);
                                 //dump('Last update true '.$coursedateOptimization->kurs_id);
                            }
                             //dump($update);
                            $loopCounter=0;
                        }

                    }
                }

            }
        //dd('Ende');
    }

    public function kurslaenge($kurslaenge){
        $time = Carbon::parse($kurslaenge);
        $hours = $time->hour;
        $minutes = $time->minute;
        $kurslaeneminuten = $hours * 60 + $minutes;

        return $kurslaeneminuten;
   }
}
