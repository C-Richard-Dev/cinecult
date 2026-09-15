<?php

namespace App\Services;

use App\Actions\Candidate\CreateCandidate;
use App\Actions\Movie\CreatePendingMovie;
use App\Actions\Movie\ReconcileAutomaticallyMovie;
use App\Enums\CompatibilityLevel;
use App\Repositories\MovieRepository;

class MovieReconciliationEngine
{
    public function __construct(
        private ArchiveService $archiveService,
        private MovieRepository $movieRepository,
        private TmdbService $tmdbService,
        private ScorerService $scorerService,
        private ReconcileAutomaticallyMovie $reconcileAutomaticallyMovie,
        private CreatePendingMovie $createPendingMovie,
        private CreateCandidate $createCandidate,
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

                    $candidates = $this->tmdbService->find($movie);

                    if ($candidates === []) {
                        continue;
                    }

                    $pendingMovie = $this->createPendingMovie->execute($movie);

                    $scoredCandidates = $this->scorerService->score($movie, $candidates);

                    foreach ($scoredCandidates as $scoredCandidate) {
                        if ($scoredCandidate->level === CompatibilityLevel::HIGH
                            || $scoredCandidate->level === CompatibilityLevel::GOOD) {
                            $videoFileName = $this->archiveService->getVideoFileName($movie->identifier);

                            if (! $videoFileName) {
                                continue;
                            }

                            $movie->videoFileName = $videoFileName;
                            $this->reconcileAutomaticallyMovie->execute($pendingMovie->id, $movie, $scoredCandidate->candidate);

                            break;
                        } else {
                            $this->createCandidate->execute($scoredCandidate->candidate, $scoredCandidate->level, $pendingMovie->id);
                        }
                    }
                }
            }

            $page++;
        } while (count($movies) === 100);
    }
}
