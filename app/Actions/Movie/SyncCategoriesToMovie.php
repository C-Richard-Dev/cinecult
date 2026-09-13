<?php

namespace App\Actions\Movie;

use App\Enums\MovieCategory;
use App\Models\{Category, Movie};

class SyncCategoriesToMovie
{
    public function execute(Movie $movie, array $categoryIds): void
    {
        $categoryIds = array_values(array_unique(array_filter($categoryIds, 'is_int')));

        if ($categoryIds === []) {
            return;
        }

        $categories = collect();

        foreach ($categoryIds as $categoryId) {
            $categoryName = $this->matchCategoryId($categoryId);

            if ($categoryName === '') {
                continue;
            }

            $categories->push(
                Category::firstOrCreate(['name' => $categoryName])
            );
        }

        if ($categories->isEmpty()) {
            return;
        }

        $movie->categories()->syncWithoutDetaching(
            $categories->pluck('id')->all()
        );
    }

    private function matchCategoryId(int $categoryId): string
    {
        return match ($categoryId) {
            MovieCategory::Action->value => 'Action',
            MovieCategory::Adventure->value => 'Adventure',
            MovieCategory::Animation->value => 'Animation',
            MovieCategory::Comedy->value => 'Comedy',
            MovieCategory::Crime->value => 'Crime',
            MovieCategory::Documentary->value => 'Documentary',
            MovieCategory::Drama->value => 'Drama',
            MovieCategory::Family->value => 'Family',
            MovieCategory::Fantasy->value => 'Fantasy',
            MovieCategory::History->value => 'History',
            MovieCategory::Horror->value => 'Horror',
            MovieCategory::Music->value => 'Music',
            MovieCategory::Mystery->value => 'Mystery',
            MovieCategory::Romance->value => 'Romance',
            MovieCategory::ScienceFiction->value => 'Science Fiction',
            MovieCategory::TVMovie->value => 'TV Movie',
            MovieCategory::Thriller->value => 'Thriller',
            MovieCategory::War->value => 'War',
            MovieCategory::Western->value => 'Western',
            default => '',
        };
    }
}