<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coursedate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trainer_id',
        'organiser_id',
        'course_id',
        'kursstarttermin',
        'kursendtermin',
        'kurslaenge',
        'kursstartvorschlag',
        'kursendvorschlag',
        'kursstartvorschlagkunde',
        'kursendvorschlagkunde',
        'sportgeraetanzahl',
        'kursInformation',
        'autor_id',
        'bearbeiter_id'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function getCousename()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'coursedate_user');
    }
}
