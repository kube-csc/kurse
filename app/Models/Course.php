<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
   use SoftDeletes;

   protected $fillable = [
             'organiser_id',
             'sportSection_id',
             'kursName',
             'kursBeschreibung',
             'kursKosten',
             'kursBezahlsystem',
             'visible',
             'trainer',
             'schnupperkurs',
             'autor_id',
             'bearbeiter_id'
   ];

    protected $dates = [
        'deleted_at'
    ];

    public function sportSection(): BelongsToMany
    {
        return $this->belongsToMany(SportSection::class);
    }
}
