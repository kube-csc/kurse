<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportSection extends Model
{
    use HasFactory;

    public function organisers()
    {
        return $this->belongsToMany('App\Organiser', 'organiser_sport_section', 'sport_section_id', 'organiser_id');
    }
}
