<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'uuid',
        'name',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $category): void {
            $category->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'category_movies');
    }
}
