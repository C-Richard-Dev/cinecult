<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use App\DTOs\ArchiveMovieDto;

class ArchiveService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.archive.url');
    }

    public function listMovies(int $page = 1, int $rows = 20): array
    {
        $datas = Http::baseUrl($this->baseUrl)
            ->get('/advancedsearch.php', [
                'q' => 'collection:movies',
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
                language: $movie['language'] ?? null,
                creator: $movie['creator'] ?? null,
                subject: isset($movie['subject'])
                    ? (array) $movie['subject']
                    : null,
            ),
            $datas['response']['docs'] ?? []
        );
    }
}