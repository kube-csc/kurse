<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
   use SoftDeletes;

   protected $fillable = [
             'sportSection_id',
             'kursName',
             'kursBeschreibung',
             'autor_id',
             'bearbeiter_id'
   ];

    protected $dates = [
        'deleted_at'
    ];
}
