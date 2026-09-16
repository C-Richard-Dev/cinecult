<?php

namespace App\Models;

use App\Enums\MovieReconciliationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable([
    'uuid',
    'reconciliation_status',
    'archive_identifier',
    'tmdb_id',
    'title',
    'original_title',
    'overview',
    'release_date',
    'poster_path',
    'backdrop_path',
    'video_file_name',
    'reconciliation_date',
])]
class Movie extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $movie): void {
            $movie->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'reconciliation_status' => MovieReconciliationStatus::class,
            'release_date' => 'date',
            'reconciliation_date' => 'date',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_movies');
    }
}
