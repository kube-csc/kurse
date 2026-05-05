<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
             'nicht_anmeldebar',
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

    public function scopeAssignableToUserInOrganiser(Builder $query, int $organiserId, int $userId): Builder
    {
        $trainerSportSectionIds = Trainertable::query()
            ->where('user_id', $userId)
            ->where('organiser_id', $organiserId)
            ->where('status', 1)
            ->pluck('sportSection_id')
            ->filter()
            ->unique()
            ->values();

        $query->where('organiser_id', $organiserId);

        if ($trainerSportSectionIds->isEmpty()) {
            return $query->where('trainer', 0);
        }

        return $query->whereHas('sportSection', function (Builder $sportSectionQuery) use ($trainerSportSectionIds) {
            $sportSectionQuery->whereIn('sport_sections.id', $trainerSportSectionIds->all());
        });
    }

    public static function isAssignableToUserInOrganiser(int $courseId, int $organiserId, int $userId): bool
    {
        return static::query()
            ->assignableToUserInOrganiser($organiserId, $userId)
            ->whereKey($courseId)
            ->exists();
    }
}
