<?php

namespace App\Models;

use App\Enums\ReconciliationRunStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'last_page_processed',
])]
class ReconciliationRun extends Model
{
    public function getTheLastPageProcessedAttribute(): int
    {
        return (int) (static::query()->latest('id')->value('last_page_processed') ?? 0);
    }
}
