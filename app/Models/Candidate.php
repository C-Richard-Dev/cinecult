<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id',
    'movie_id',
    'tmdb_id',
    'title',
    'overview',
    'release_date',
    'level',
])]
class Candidate extends Model
{
    protected $casts = [
        'level' => \App\Enums\CompatibilityLevel::class,
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
