<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Enums\MovieConciliationStatus;

#[Fillable([
    'uuid',
    'conciliation_status',
    'archive_identifier',
    'tmdb_id',
    'title',
    'original_title',
    'overview',
    'release_date',
    'poster_path',
    'backdrop_path',
    'video_file_name',
    'conciliation_date',
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
            'conciliation_status' => MovieConciliationStatus::class,
            'release_date' => 'date',
            'conciliation_date' => 'date',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_movies');
    }
}
