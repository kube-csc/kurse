<?php

namespace App\Http\Controllers;

use App\Components\FlashMessages;
use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use App\Models\Organiser;
use App\Models\SportEquipment;
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

    public function timeOptimizationTrainer($cousedateId, $coursedate, $coursedateOptimizationAlls, $coursedateOptimizationBoockeds, $coursedateOptimizations, $trainer)
    {
            $statusFirst=0;
            $coursedateOptimizationBoockedDell=0;
            foreach ($coursedateOptimizations as $coursedateOptimization) {
                // Termin verschieben die nur einen freien Termin im Zeitfenster gebuchten Termin
                if($coursedateOptimizations->count()==1){
                    $kurslaeneminuten = $this->kurslaenge($coursedateOptimization->kurslaenge);
                    $loopCounter=0;
                    foreach ($coursedateOptimizationBoockeds as $coursedateOptimizationBoocked) {
                        $loopCounter++;
                        if ($loopCounter==1) {
                            $diffMinute = Carbon::parse($coursedateOptimization->kursstartvorschlag)->diffInMinutes(Carbon::parse($coursedateOptimizationBoocked->kursstarttermin));
                            if ($kurslaeneminuten <= $diffMinute) {
                                $fruesterStarttermin = $coursedateOptimization->kursstartvorschlag;
                                $spaetesterEndtermin = $coursedateOptimizationBoocked->kursstarttermin;
                            }
                            $naesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                        }
                        if($loopCounter>1 and $coursedateOptimizationBoockeds->count()>$loopCounter) {
                            $diffMinute = Carbon::parse($coursedateOptimizationBoocked->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimizationBoocked->kursstarttermin));
                            if ($kurslaeneminuten <= $diffMinute) {
                                if(!isset($fruesterStarttermin)) {
                                    $fruesterStarttermin = $coursedateOptimization->kursstartvorschlag;
                                }
                                $spaetesterEndtermin = $coursedateOptimizationBoocked->kursstarttermin;
                            }
                            $naesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                        }
                        if($coursedateOptimizationBoockeds->count()==$loopCounter) {
                            $diffMinute = Carbon::parse($coursedateOptimizationBoocked->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendvorschlag));
                            if ($kurslaeneminuten <= $diffMinute) {
                                if(!isset($fruesterStarttermin)) {
                                    $fruesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                                }
                                $spaetesterEndtermin = $coursedateOptimizationBoocked->kursendvorschlag;
                            }
                        }
                    }
                    $update = Coursedate::find($coursedateOptimization->coursedate_id);
                    if(isset($fruesterStarttermin) and isset($spaetesterEndtermin)) {
                        // ToDo: Noch nicht getestet
                        $sportgeraetanzahlfree=$this->bookedCountDate($update, $fruesterStarttermin, $spaetesterEndtermin);
                        if($sportgeraetanzahlfree>0) {
                            $update->update([
                                'kursstarttermin'       => $fruesterStarttermin,
                                'kursendtermin'         => $spaetesterEndtermin,
                                'kursNichtDurchfuerbar' => false
                            ]);
                        }
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
                        $coursedateOptimizationBoockeds = $coursedateOptimizationBoockeds->whereNotIn('id', $coursedateOptimizationBoockedDell);
                        $coursedateOptimizationBoockedDell=0;
                    }
                    foreach ($coursedateOptimizationBoockeds as $coursedateOptimizationBoocked) {
                        $loopCounter++;
                        if($loopCounter==1){
                            if ($statusFirst==0) {
                                $statusFirst = 1;
                                $fruesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                                $diffMinute = Carbon::parse($coursedateOptimization->kursstartvorschlag)->diffInMinutes(Carbon::parse($coursedateOptimizationBoocked->kursstarttermin));
                                if ($kurslaeneminuten <= $diffMinute) {
                                    $update = Coursedate::find($coursedateOptimization->coursedate_id);
                                    $sportgeraetanzahlfree=$this->bookedCountDate($update, $coursedateOptimization->kursstartvorschlag, $coursedateOptimizationBoocked->kursstarttermin);
                                    if($sportgeraetanzahlfree>0) {
                                        $update->update([
                                            'kursstarttermin'       => $coursedateOptimization->kursstartvorschlag,
                                            'kursendtermin'         => $coursedateOptimizationBoocked->kursstarttermin,
                                            'kursNichtDurchfuerbar' => false
                                        ]);
                                    }
                                    break;
                                }
                           }
                           $coursedateOptimizationBoockedDell=$coursedateOptimizationBoocked->id;
                        }

                        if($loopCounter>1) {
                            $diffMinute = Carbon::parse($fruesterStarttermin)->diffInMinutes(Carbon::parse($coursedateOptimizationBoocked->kursstarttermin));
                            if($fruesterStarttermin>$coursedateOptimizationBoocked->kursstarttermin){
                                $diffMinute = $diffMinute * -1;
                            }
                            if ($kurslaeneminuten <= $diffMinute) {
                                $update = Coursedate::find($coursedateOptimization->coursedate_id);
                                if($coursedateOptimizationBoockeds->count()==1) {
                                    $sportgeraetanzahlfree=$this->bookedCountDate($update, $fruesterStarttermin, $coursedateOptimization->kursendvorschlag);
                                    if($sportgeraetanzahlfree>0) {
                                        $update->update([
                                            'kursstarttermin'       => $fruesterStarttermin,
                                            'kursendtermin'         => $coursedateOptimization->kursendvorschlag,
                                            'kursNichtDurchfuerbar' => false
                                        ]);
                                    }
                                }
                                else {
                                    $sportgeraetanzahlfree=$this->bookedCountDate($update, $fruesterStarttermin, $coursedateOptimizationBoocked->kursstarttermin);
                                    if($sportgeraetanzahlfree>0) {
                                        $update->update([
                                            'kursstarttermin'       => $fruesterStarttermin,
                                            'kursendtermin'         => $coursedateOptimizationBoocked->kursstarttermin,
                                            'kursNichtDurchfuerbar' => false
                                        ]);
                                    }
                                }
                                $fruesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                                break;
                            }
                            $fruesterStarttermin = $coursedateOptimizationBoocked->kursendtermin;
                        }

                        if($coursedateOptimizationBoockeds->count()==$loopCounter){
                            $diffMinute = Carbon::parse($coursedateOptimizationBoocked->kursendtermin)->diffInMinutes(Carbon::parse($coursedateOptimization->kursendvorschlag));
                            $update = Coursedate::find($coursedateOptimization->coursedate_id);
                            if ($kurslaeneminuten <= $diffMinute) {
                                $update = Coursedate::find($coursedateOptimization->coursedate_id);
                                $sportgeraetanzahlfree=$this->bookedCountDate($update, $coursedateOptimizationBoocked->kursendtermin, $coursedateOptimization->kursendvorschlag);
                                if($sportgeraetanzahlfree>0) {
                                    $update->update([
                                        'kursstarttermin'       => $coursedateOptimizationBoocked->kursendtermin,
                                        'kursendtermin'         => $coursedateOptimization->kursendvorschlag,
                                        'kursNichtDurchfuerbar' => false
                                    ]);
                                }
                                else{
                                    $update->update([
                                        'kursstarttermin'       => $coursedateOptimizationBoocked->kursendtermin,
                                        'kursendtermin'         => $coursedateOptimization->kursendvorschlag,
                                        'kursNichtDurchfuerbar' => true
                                    ]);

                                }
                            }
                            else{
                                $update->update([
                                    'kursstarttermin'       => $coursedateOptimizationBoocked->kursendtermin,
                                    'kursendtermin'         => $coursedateOptimization->kursendvorschlag,
                                    'kursNichtDurchfuerbar' => true
                                ]);
                            }
                            $loopCounter=0;
                        }

                    }
                }
            }
    }

    public function kurslaenge($kurslaenge){
        $time = Carbon::parse($kurslaenge);
        $hours = $time->hour;
        $minutes = $time->minute;
        $kurslaeneminuten = $hours * 60 + $minutes;

        return $kurslaeneminuten;
   }

    public function bookedCountDate($coursedate, $kursstarttermin, $kursendtermin)
    {
        //ToDo: Auf Sportplätze umstellen ->sum('sportleranzahl')

        $courseBookedCount = CourseParticipantBooked::where('kurs_id', $coursedate->id)->count();

        // Alle Sportgeräte
        $sportEquipmentCount = Coursedate::
              join('course_sport_section', 'course_sport_section.course_id', '=', 'coursedates.course_id')
            ->join('sport_equipment', 'sport_equipment.sportSection_id', '=', 'course_sport_section.sport_section_id')
            ->where('coursedates.id', $coursedate->id)
            ->count();

        // Belegte Boote
        $sportEquipmentBookedCount = SportEquipment::join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
            ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
            ->where('sport_equipment_bookeds.deleted_at', null)
            ->where('coursedates.kursstarttermin', '<', $kursendtermin)
            ->where('coursedates.kursendtermin', '>', $kursstarttermin)
            ->count();

        return $sportgeraetanzahlfree=$sportEquipmentCount-$sportEquipmentBookedCount-($courseBookedCount-$sportEquipmentBookedCount);
    }
}
