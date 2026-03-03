<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SportEquipmentBooked extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sportgeraet_id',
        'kurs_id',
        'user_id',
        'bearbeiter_id',
    ];

    protected $dates = [
        'deleted_at'
    ];
}
