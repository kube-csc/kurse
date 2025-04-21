<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Training extends Model
{
    protected $fillable = [
        'datumAktuell'
    ];

    public function sportSection(): BelongsTo
    {
        return $this->belongsTo(SportSection::class, 'sportSection_id');
    }
}
