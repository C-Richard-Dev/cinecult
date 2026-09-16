<?php

namespace App\Models;

use App\Enums\ReconciliationRunStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'status',
    'started_at',
    'finished_at',
    'movies_created',
    'candidates_created',
    'movies_reconciled',
])]
class ReconciliationRun extends Model
{
    protected $casts = [
        'status' => ReconciliationRunStatus::class,
    ];

    public function getTheLastPageProcessedAttribute(): int
    {
        return (int) (static::query()->latest('id')->value('last_page_processed') ?? 0);
    }
}
