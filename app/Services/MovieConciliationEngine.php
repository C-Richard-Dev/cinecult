<?php

namespace App\Services;

use App\Repositories\MovieRepository;
use App\Services\TmdbService;
use App\Services\ArchiveService;
use App\Services\ScorerService;
use App\Actions\Movie\ConciliateAutomaticallyMovie;
use App\Actions\Movie\CreatePendingMovie;
use App\Enums\CompatibilityLevel;

class MovieConciliationEngine
{
    public function __construct(
        private ArchiveService $archiveService,
        private MovieRepository $movieRepository,
        private TmdbService $tmdbService,
        private ScorerService $scorerService,
        private ConciliateAutomaticallyMovie $conciliateAutomaticallyMovie,
        private CreatePendingMovie $createPendingMovie,
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
                        if ($scoredCandidate->level === CompatibilityLevel::HIGH) {
                            $videoFileName = $this->archiveService->getVideoFileName($movie->identifier);
                            
                            if (!$videoFileName) {
                                continue;
                            }

                            $movie->videoFileName = $videoFileName;
                            $this->conciliateAutomaticallyMovie->execute($pendingMovie->id, $movie, $scoredCandidate->candidate);

                            break;
                        } else { 
                            // TODO: cria um candidato ao filme no banco
                        }

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
}
