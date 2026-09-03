<?php

namespace App\Models;

use App\Enums\MovieCategory;
use App\Enums\MovieConciliationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'conciliation_status',
    'category',
    'archive_identifier',
    'tmdb_id',
    'title',
    'original_title',
    'overview',
    'release_date',
    'runtime',
    'poster_path',
    'backdrop_path',
])]
class Movie extends Model
{
    protected function casts(): array
    {
        return [
            'conciliation_status' => MovieConciliationStatus::class,
            'category' => MovieCategory::class,
            'release_date' => 'date',
            'runtime' => 'integer',
        ];
    }
}
