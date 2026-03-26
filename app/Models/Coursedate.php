<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coursedate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organiser_id',
        'course_id',
        'training_id',
        'event_id',
        'kursstarttermin',
        'kursendtermin',
        'kurslaenge',
        'kursstartvorschlag',
        'kursendvorschlag',
        'kursstartvorschlagkunde',
        'kursendvorschlagkunde',
        'kursNichtDurchfuerbar',
        'kursFahrtenlaenge',
        'sportgeraetanzahl',
        'sportgeraeteReserviert',
        'kursInformation',
        'autor_id',
        'bearbeiter_id'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $casts = [
        'kursFahrtenlaenge' => 'float',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function getCousename(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'coursedate_user')
            ->withPivot(['trainerFahrtenlaenge'])
            ->withTimestamps();
    }

    public function courseParticipantBookeds(): HasMany
    {
        return $this->hasMany(CourseParticipantBooked::class, 'kurs_id');
    }

    public function getOrganiserName(): BelongsTo
    {
        return $this->belongsTo(Organiser::class, 'organiser_id');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function getSportSectionAbteilung()
    {
        return $this->training?->sportSection?->abteilung;
    }
}
