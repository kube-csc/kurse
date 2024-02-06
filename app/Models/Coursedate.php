<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coursedate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trainer_id',
        'sportSection_id',
        'course_id',
        'kursstarttermin',
        'kursendtermin',
        'kurslaenge',
        'kursstartvorschlag',
        'kursendvorschlag',
        'sportgeraetanzahl',
        'user_id',
        'bearbeiter_id'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function getCousename()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function getTrainerName()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

}
