<?php

namespace App\Services;

use App\Repositories\MovieRepository;
use App\Services\TmdbService;
use App\Services\ArchiveService;
use App\Services\ScorerService;

class MovieConciliationEngine
{
    public function __construct(
        private ArchiveService $archiveService,
        private MovieRepository $movieRepository,
        private TmdbService $tmdbService,
        private ScorerService $scorerService,
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

                    $candidatesScored = $this->scorerService->score($movie, $candidates);

                    foreach ($candidatesScored as $candidate) {

                        if ($candidate['score'] === 100) {
                            // cria o filme no repositorio (conciliado)
                            // break para sair do loop de candidatos, pois já encontramos uma correspondência perfeita
                            break;
                        } else if($candidate['score'] >= 80) { 
                            // cria um candidato ao filme no banco
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
