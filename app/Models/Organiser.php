<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organiser extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'veranstalter',
        'veranstalterBild',
        'veranstalterBeschreibungLang',
        'veranstalterBeschreibungKurz',
        'sportartBeschreibungLang',
        'sportartBeschreibungKurz',
        'keineKurse',
        'veranstalterDomain',
        'user_id',
        'bearbeiter_id'
    ];

    //ToDo: Wird dieses Benötigt?
    public function sportSections()
    {
        return $this->belongsToMany('App\SportSection', 'organiser_sport_section', 'organiser_id', 'sport_section_id');
    }

    public function sportSection()
    {
        return $this->belongsToMany(SportSection::class);
    }
}
