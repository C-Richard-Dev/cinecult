<?php

namespace App\Services;

use App\DTOs\ArchiveMovieDto;
use App\DTOs\TmdbMovieDto;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

class TmdbService
{
    private string $baseUrl;

    private string $apiKey;

    private int $connectTimeout;

    private int $timeout;

    private int $requestDelayMs;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.url');
        $this->apiKey = config('services.tmdb.api_key') ?? '';
        $this->connectTimeout = config('services.tmdb.connect_timeout');
        $this->timeout = config('services.tmdb.timeout');
        $this->requestDelayMs = config('services.tmdb.request_delay_ms');
    }

    public function testConnection(): array
    {
        $this->throttle();

        try {
            $response = Http::baseUrl($this->baseUrl)
                ->connectTimeout($this->connectTimeout)
                ->timeout($this->timeout)
                ->get('/search/movie', [
                    'api_key' => $this->apiKey,
                    'query' => 'The Matrix',
                ])
                ->throw();

            return array_map(
                fn (array $candidate) => new TmdbMovieDto(
                    id: (int) ($candidate['id'] ?? 0),
                    adult: (bool) ($candidate['adult'] ?? false),
                    backdropPath: $candidate['backdrop_path'] ?? null,
                    genreIds: $candidate['genre_ids'] ?? [],
                    title: $candidate['title'] ?? null,
                    originalLanguage: $candidate['original_language'] ?? null,
                    originalTitle: $candidate['original_title'] ?? null,
                    overview: $candidate['overview'] ?? null,
                    popularity: isset($candidate['popularity']) ? (float) $candidate['popularity'] : null,
                    posterPath: $candidate['poster_path'] ?? null,
                    releaseDate: $candidate['release_date'] ?? null,
                    video: (bool) ($candidate['video'] ?? false),
                    voteAverage: isset($candidate['vote_average']) ? (float) $candidate['vote_average'] : null,
                    voteCount: isset($candidate['vote_count']) ? (int) $candidate['vote_count'] : null,
                ),
                $response->json('results') ?? []
            );
        } catch (\Exception $e) {
            return $e->getMessage() ? ['error' => $e->getMessage()] : ['error' => 'Unknown error occurred'];
        }
    }

    public function find(ArchiveMovieDto $movie): array
    {
        $this->throttle();

        $params = array_filter([
            'api_key' => $this->apiKey,
            'query' => $movie->title,
        ], fn ($value) => $value !== null && $value !== false && $value !== '');

        $response = Http::baseUrl($this->baseUrl)
            ->connectTimeout($this->connectTimeout)
            ->timeout($this->timeout)
            ->get('/search/movie', $params)
            ->throw();

        return array_map(
            fn (array $candidate) => new TmdbMovieDto(
                id: $candidate['id'],
                adult: $candidate['adult'],
                backdropPath: $candidate['backdrop_path'] ?? null,
                genreIds: $candidate['genre_ids'] ?? [],
                title: $candidate['title'] ?? null,
                originalLanguage: $candidate['original_language'] ?? null,
                originalTitle: $candidate['original_title'] ?? null,
                overview: $candidate['overview'] ?? null,
                popularity: $candidate['popularity'] ?? null,
                posterPath: $candidate['poster_path'] ?? null,
                releaseDate: $candidate['release_date'] ?? null,
                video: $candidate['video'],
                voteAverage: $candidate['vote_average'] ?? null,
                voteCount: $candidate['vote_count'] ?? null,
            ),
            $response->json('results') ?? []
        );
    }

    /**
     * Waits before sending a new request to avoid overwhelming the TMDB API.
     */
    private function throttle(): void
    {
        Sleep::for($this->requestDelayMs)->milliseconds();
    }
}
