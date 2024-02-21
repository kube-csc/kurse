<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SportEquipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sportgeraet',
        'anschafdatum',
        'verschrottdatum',
        'sportleranzahl',
        'laenge',
        'breite',
        'hoehe',
        'gewicht',
        'tragkraft',
        'typ',
        'user_id',
        'bearbeiter_id'
    ];

    protected $dates = [
        'deleted_at'
    ];
}
