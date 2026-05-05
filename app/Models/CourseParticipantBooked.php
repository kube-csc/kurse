<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseParticipantBooked extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trainer_id',
        'mitglied_id',
        'participant_id',
        'regattaTeam_id',
        'kurs_id',
        'teilnehmerFahrtenlaenge',
        'user_id',
        'bearbeiter_id',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $casts = [
        'teilnehmerFahrtenlaenge' => 'float',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Coursedate::class, 'kurs_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(CourseParticipant::class, 'participant_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
