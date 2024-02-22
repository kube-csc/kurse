<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organiser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'veranstalter',
        'veranstalterBild',
        'veranstalterBeschreibung',
        'veranstalterrDomain',
        'bearbeiter_id',
        'user_id'
    ];

    public function sportSections()
    {
        return $this->belongsToMany('App\SportSection', 'organiser_sport_section', 'organiser_id', 'sport_section_id');
    }
}
