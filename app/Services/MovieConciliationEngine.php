<?php

namespace App\Services;

use App\Repositories\MovieRepository;
use App\Services\TmdbService;
use App\Services\ArchiveService;

class MovieConciliationEngine
{
    public function __construct(
        private ArchiveService $archiveService,
        private MovieRepository $movieRepository,
        private TmdbService $tmdbService,
    ) {}

    public function run(): void
    {
        $page = 1;

        do {
            $movies = $this->archiveService->listMovies(page: $page, rows: 100);

            if ($movies === []) {
                break;
            }

            foreach (array_chunk($movies, 100) as $moviesChunk) {
                foreach ($moviesChunk as $movie) {
                    if ($this->movieRepository->findByArchiveIdentifier($movie->identifier)) {
                        continue;
                    }

                    $this->tmdbService->find($movie);
                }
            }

            $page++;
        } while (count($movies) === 100);
    }
}
