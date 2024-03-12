<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organiserinformation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organiser_id',
        'veranstaltungHeader',
        'veranstaltungBeschreibungLang',
        'veranstaltungBeschreibungKurz',
        'sportartBeschreibungLang',
        'sportartBeschreibungKurz',
        'materialUeberschrift',
        'materialBeschreibungLang',
        'materialBeschreibungKurz',
        'keineKurse',
        'terminInformation',
        'mitgliedschaftKurz',
        'mitgliedschaftLang',
        'autor_id',
        'bearbeiter_id',
        'freigeber_id',
        'letzteFreigabe'
    ];
}
