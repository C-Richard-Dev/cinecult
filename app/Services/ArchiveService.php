<?php

namespace App\Services;

use App\DTOs\ArchiveMovieDto;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

class ArchiveService
{
    private string $baseUrl;

    private int $connectTimeout;

    private int $timeout;

    private int $requestDelayMs;

    public function __construct()
    {
        $this->baseUrl = config('services.archive.url');
        $this->connectTimeout = config('services.archive.connect_timeout');
        $this->timeout = config('services.archive.timeout');
        $this->requestDelayMs = config('services.archive.request_delay_ms');
    }

    public function listMovies(int $page = 1, int $rows = 20): array
    {
        $this->throttle();

        $datas = Http::baseUrl($this->baseUrl)
            ->connectTimeout($this->connectTimeout)
            ->timeout($this->timeout)
            ->get('/advancedsearch.php', [
                'q' => 'collection:feature_films_unsorted',
                'fl' => 'identifier,title,description,year,date,language,creator,subject',
                'rows' => $rows,
                'page' => $page,
                'output' => 'json',
            ])
            ->throw()
            ->json();

        return array_map(
            fn (array $movie) => new ArchiveMovieDto(
                identifier: $movie['identifier'],
                title: $movie['title'],
                description: $movie['description'] ?? null,
                year: isset($movie['year']) ? (int) $movie['year'] : null,
                date: $movie['date'] ?? null,
                language: $this->normalizeToString($movie['language'] ?? null),
                creator: $this->normalizeToString($movie['creator'] ?? null),
                subject: isset($movie['subject'])
                    ? (array) $movie['subject']
                    : null,
            ),
            $datas['response']['docs'] ?? []
        );
    }

    public function getVideoFileName(string $identifier): ?string
    {
        $this->throttle();

        $data = Http::baseUrl($this->baseUrl)
            ->connectTimeout($this->connectTimeout)
            ->timeout($this->timeout)
            ->get("/metadata/{$identifier}")
            ->throw()
            ->json();

        foreach ($data['files'] ?? [] as $file) {
            if (($file['format'] ?? null) === 'MPEG4') {
                return $file['name'];
            }
        }

        return null;
    }

    /**
     * Waits before sending a new request to avoid overwhelming the Internet Archive API.
     */
    private function throttle(): void
    {
        Sleep::for($this->requestDelayMs)->milliseconds();
    }

    /**
     * The Internet Archive API returns some fields (e.g. creator, language)
     * as a single string or as an array of strings when a movie has
     * multiple values. Normalize either shape into a single string.
     *
     * @param  array<int, string>|string|null  $value
     */
    private function normalizeToString(array|string|null $value): ?string
    {
        if (is_array($value)) {
            return $value === [] ? null : implode(', ', $value);
        }

        return $value;
    }
}
