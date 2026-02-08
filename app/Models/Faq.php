<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'category',
        'category_sort_order',
        'question',
        'answer_html',
        'sort_order',
        'is_active',
        'eventGroup_id',
        'event_id',
        'organisers_id',
        'use_organisers',
    ];

    protected $casts = [
        'category_sort_order' => 'int',
        'sort_order' => 'int',
        'is_active' => 'bool',
        'use_organisers' => 'bool',
        'eventGroup_id' => 'int',
        'event_id' => 'int',
        'organisers_id' => 'int',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Sichtbarkeit gem. Projektlogik:
     * - nur Datensätze mit use_organisers = 1
     * - organisers_id ist NULL (global) ODER entspricht dem aktuellen Organiser
     * - eventGroup_id/event_id werden bewusst ignoriert
     */
    public function scopeVisibleForOrganiser(Builder $query, ?int $organiserId): Builder
    {
        return $query
            ->where('use_organisers', true)
            ->where(function (Builder $q) use ($organiserId) {
                $q->whereNull('organisers_id');

                if ($organiserId !== null) {
                    $q->orWhere('organisers_id', $organiserId);
                }
            });
    }
}
