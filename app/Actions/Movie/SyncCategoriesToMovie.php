<?php

namespace App\Actions\Movie;

use App\Models\{Category, Movie};
use App\Models\Pivot\CategoryMovie;

class SyncCategoriesToMovie
{
    public function execute(Movie $movie, array $categoryIds): void
    {
        foreach ($categoryIds as $categoryId) {
            $categoryName = $this->matchCategoryId($categoryId);
            $category = Category::where('name', $categoryName)->first();

            if (!$category) {
                continue;
            }

            CategoryMovie::create([
                'movie_id' => $movie->id,
                'category_id' => $category->id,
            ]);
        }
    }

    private function matchCategoryId(int $categoryId): string
    {
        switch ($categoryId) {
            case 28:
                return 'Action';
            case 12:
                return 'Adventure';
            case 16:
                return 'Animation';
            case 35:
                return 'Comedy';
            case 80:
                return 'Crime';
            case 99:
                return 'Documentary';
            case 18:
                return 'Drama';
            case 10751:
                return 'Family';
            case 14:
                return 'Fantasy';
            case 36:
                return 'History';
            case 27:
                return 'Horror';
            case 10402:
                return 'Music';
            case 9648:
                return 'Mystery';
            case 10749:
                return 'Romance';
            case 878:
                return 'Science Fiction';
            case 10770:
                return 'TV Movie';
            case 53:
                return 'Thriller';
            case 10752:
                return 'War';
            case 37:
                return 'Western';
            default:
                return '';
        }
    }
}