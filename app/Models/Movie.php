<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Movie extends Model
{
    protected $fillable = [
        'uuid',
        'conciliation_status',
        'archive_identifier',
        'tmdb_id',
        'title',
        'original_title',
        'overview',
        'release_date',
        'runtime',
        'poster_path',
        'backdrop_path',
        'video_file_name',
    ];

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
            'release_date' => 'date',
            'runtime' => 'integer',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_movies');
    }
}
