<?php

namespace App\Mail;

use App\Models\Coursedate;
use App\Models\CourseParticipantBooked;
use App\Models\Organiser;
use App\Models\SportEquipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainerMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $coursedates, $trainer;

    /**
     * Create a new message instance.
     */
    public function __construct($coursedates, $trainer)
    {
        $this->coursedates = $coursedates;
        $this->trainer = $trainer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Trainer Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        $mailtext = "";
        foreach ($this->coursedates as $coursedate) {

            // Belegte Boote andere Kurse
            $sportEquipmentBookeds = SportEquipment::
                  join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
                ->where('sport_equipment_bookeds.deleted_at', null)
                ->whereNot('sport_equipment_bookeds.kurs_id', $coursedate->coursedate_id)
                ->join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
                ->where('organiser_sport_section.organiser_id', $coursedate->organiser_id)
                ->join('coursedates', 'coursedates.id', '=', 'sport_equipment_bookeds.kurs_id')
                ->where('coursedates.kursstarttermin', '<', $coursedate->kursendtermin)
                ->where('coursedates.kursendtermin', '>', $coursedate->kursstarttermin)
                ->join('coursedate_user', 'coursedate_user.coursedate_id', '=', 'coursedates.id')
                ->join('users', 'users.id', '=', 'coursedate_user.user_id')
                ->orderBy('sport_equipment.sportgeraet')
                //->distinct()
                ->get();

            // Gebuchte Boote für den Kurs
            $sportEquipmentKursBookeds = SportEquipment::join('sport_equipment_bookeds', 'sport_equipment_bookeds.sportgeraet_id', '=', 'sport_equipment.id')
                ->where('sport_equipment_bookeds.deleted_at', null)
                ->where('sport_equipment_bookeds.kurs_id', $coursedate->coursedate_id)
                ->orderBy('sport_equipment.sportgeraet')
                ->get();

            $mailtext = $mailtext . "<b>Termin Name:</b> " . $coursedate->getCousename->kursName . "<br><br>";
            $mailtext = $mailtext . "Datum: " . date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) . " Uhr bis " . date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) . " Uhr<br>";
            $mailtext = $mailtext . "Dauer: " . date('H:i', strtotime($coursedate->kurslaenge)) . " Stunde(n)<br><br>";
            if ($coursedate->kursInformation != null) {
                $mailtext = $mailtext . "<b>Termininformation:</b> " . $coursedate->kursInformation . "<br><br>";
            }

            $courseParticipantBookeds = CourseParticipantBooked::where('kurs_id', $coursedate->id)->get();

            $trainerNamen = "Folgende Trainer sind für den Termin eingetragen:<br>";
            foreach ($coursedate->users as $user) {
                $trainerNamen = $trainerNamen . $user->vorname . " " . $user->nachname . "<br>";
            }

            $mailtext = $mailtext . $trainerNamen . "<br>";

            $participant = "<b>Folgende Teilnehmer sind für den Termin eingetragen:</b><br>";
            foreach ($courseParticipantBookeds as $courseParticipantBooked) {
                if ($courseParticipantBooked->participant_id > 0) {
                    $participant = $participant . "Name: " . $courseParticipantBooked->participant->nachname . " " . $courseParticipantBooked->participant->vorname . "<br>";
                    $participant = $participant . "Telefon: " . $courseParticipantBooked->participant->telefon . "<br>";
                    $participant = $participant . "E-Mail: " . $courseParticipantBooked->participant->email . "<br>";

                    if ($courseParticipantBooked->participant->nachricht != null) {
                        $praticipant = $participant . "Nachricht: " . $courseParticipantBooked->participant->nachricht . "<br>";
                    }
                }

                if ($courseParticipantBooked->trainer_id > 0) {
                    $participant = $participant . "Teilnehmer gebucht durch: " . $courseParticipantBooked->trainer->nachname . " " . $courseParticipantBooked->trainer->vorname . "<br>";
                }
            }

            $mailtext = $mailtext . $participant . "<br>";

            $gebuchteSportgeraete = "<b>Folgende Sportgeräte sind für den Termin gebucht:</b><br>";
            foreach ($sportEquipmentKursBookeds as $sportEquipmentKursBooked) {
                $gebuchteSportgeraete = $gebuchteSportgeraete.$sportEquipmentKursBooked->sportgeraet."<br>";
            }
            $mailtext = $mailtext .$gebuchteSportgeraete."<br>";

            $belegtSportgeraeteAndereKurse = "<b>Folgende Sportgeräte sind von anderen Terminen belegt:</b><br>";
            $sporgeraeteIdVorher=0;
            foreach($sportEquipmentBookeds as $sportEquipmentBooked) {
                if($sporgeraeteIdVorher!=$sportEquipmentBooked->sportgeraet_id) {
                  $belegtSportgeraeteAndereKurse = $belegtSportgeraeteAndereKurse . $sportEquipmentBooked->sportgeraet . " gebucht von: " . $sportEquipmentBooked->vorname . " " . $sportEquipmentBooked->nachname . "<br>";
                }
                else {
                    $belegtSportgeraeteAndereKurse = $belegtSportgeraeteAndereKurse . " und " . $sportEquipmentBooked->vorname . " " . $sportEquipmentBooked->nachname . "<br>";
                }
                $sporgeraeteIdVorher=$sportEquipmentBooked->sportgeraet_id;
            }
            $mailtext = $mailtext.$belegtSportgeraeteAndereKurse."<br>";

            $mailtext = $mailtext."<br><br><br><br>";
        }

        return new Content(
            markdown: 'mail.trainer.booked',
            with: [
                'mailtext' => $mailtext,
                'trainer' => $this->trainer,
           ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
