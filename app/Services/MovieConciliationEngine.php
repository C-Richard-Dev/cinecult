<?php

namespace App\Services;

use App\Repositories\{ArchiveRepository, MovieRepository};
use App\Services\TmdbService;
use App\Services\ArchiveService;
use App\Services\ScorerService;
use App\Actions\Movie\CreateMovie;
use App\Actions\ArchiveMovie\CreateArchiveMovie;
use App\Models\ArchiveMovie;
use App\DTOs\{CandidateScoreDto, ArchiveMovieDto};

class MovieConciliationEngine
{
    public function __construct(
        private ArchiveService $archiveService,
        private MovieRepository $movieRepository,
        private ArchiveRepository $archiveRepository,
        private TmdbService $tmdbService,
        private ScorerService $scorerService,
        private CreateMovie $createMovie,
        private CreateArchiveMovie $createArchiveMovieAction,
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
                    if (!$this->validateMovieInApp($movie->identifier)) {
                        continue;
                    }

                    $candidates = $this->tmdbService->find($movie);

                    if ($candidates === []) {
                        continue;
                    }

                    $bestCandidate = $this->getTheBestCandidate($movie, $candidates);

                    if ($bestCandidate->score >= 80) {
                        $videoFileName = $this->archiveService->getVideoFileName($movie->identifier);

                        if (!$videoFileName) {
                            continue;
                        }
                        $movie->videoFileName = $videoFileName;
                        $this->createMovie->execute($movie, $bestCandidate);
                    } else {
                        $this->createArchiveMovieAction->execute($movie);
                    }
                }
            }
             
            $page++;
        } while (count($movies) === 100);
    }

    // para debug
    public function runTest(): void
    {
        $movies = $this->archiveService->listMovies(
            page: 1,
            rows: 10
        );

        foreach ($movies as $movie) {

            if ($this->movieRepository->findByArchiveIdentifier($movie->identifier)) {
                continue;
            }

            $candidates = $this->tmdbService->find($movie);

            dump([
                'archive' => $movie,
                'candidates' => $candidates,
            ]);
        }
    }

    private function validateMovieInApp(string $identifier): bool
    {
        if ($this->movieRepository->findByArchiveIdentifier($identifier)) {
            return false;
        }

        if ($this->archiveRepository->findByIdentifier($identifier)) {
            return false;
        }

        return true;
    }

    private function getTheBestCandidate(ArchiveMovieDto $movie, array $candidates): ?CandidateScoreDto
    {
        $scoredCandidates = $this->scorerService->score($movie, $candidates);
        return $scoredCandidates[0] ?? null;
    }
}
