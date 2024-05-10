<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseParticipantBooked extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trainer_id',
        'mitglied_id',
        'participant_id',
        'regattaTeam_id',
        'kurs_id'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function participant()
    {
        return $this->belongsTo(CourseParticipant::class);
    }

    public function trainer()
    {
        return $this->belongsTo(CourseParticipant::class);
    }
}
