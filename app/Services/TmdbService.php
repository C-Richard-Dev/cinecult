<?php

namespace App\Services;

use App\DTOs\ArchiveMovieDto;
use Illuminate\Support\Facades\Http;

class TmdbService
{
    private string $baseUrl;

    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.url');
        $this->apiKey = config('services.tmdb.api_key');
    }

    public function find(ArchiveMovieDto $movie): array
    {
        $params = array_filter([
            'api_key' => $this->apiKey,
            'query' => $movie->title,
            'year' => $movie->year,
            'language' => $movie->language,
            'page' => 1,
            'include_adult' => false,
            'region' => null,
        ], fn ($value) => $value !== null && $value !== false && $value !== '');

        $candidates = Http::baseUrl($this->baseUrl)
            ->get('/search/movie', $params)
            ->throw();

        return $candidates->json('results') ?? [];
    }
}
