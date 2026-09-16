<?php

namespace App\Services;

use App\Actions\Candidate\CreateCandidate;
use App\Actions\Movie\CreatePendingMovie;
use App\Actions\Movie\ReconcileAutomaticallyMovie;
use App\Enums\CompatibilityLevel;
use App\Models\ReconciliationRun;
use App\Models\Movie;

class MovieReconciliationEngine
{
    private const PAGES_PER_RUN = 2;

    private int $moviesCreated = 0;

    private int $candidatesCreated = 0;

    private int $moviesReconciled = 0;

    public function __construct(
        private ArchiveService $archiveService,
        private TmdbService $tmdbService,
        private ScorerService $scorerService,
        private ReconcileAutomaticallyMovie $reconcileAutomaticallyMovie,
        private CreatePendingMovie $createPendingMovie,
        private CreateCandidate $createCandidate,
    ) {}

    /**
     * Runs the reconciliation for up to PAGES_PER_RUN pages starting
     * from the given page and returns the last fully processed page.
     */
    public function run(int $startPage, ReconciliationRun $reconciliationRun): int
    {
        $page = $startPage;
        $lastPageToProcess = $startPage + self::PAGES_PER_RUN - 1;

        do {
            $movies = $this->archiveService->listMovies(page: $page, rows: 100);

            if ($movies === []) {
                break;
            }

            foreach (array_chunk($movies, 100) as $moviesChunk) {
                foreach ($moviesChunk as $movie) {
                    if ($this->findByArchiveIdentifier($movie->identifier)) {
                        continue;
                    }

                    $candidates = $this->tmdbService->find($movie);

                    if ($candidates === []) {
                        continue;
                    }

                    $pendingMovie = $this->createPendingMovie->execute($movie);
                    $this->moviesCreated++;

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

                            $this->moviesReconciled++;

                            break;
                        } else {
                            $this->createCandidate->execute($scoredCandidate->candidate, $scoredCandidate->level, $pendingMovie->id);
                            $this->candidatesCreated++;
                        }
                    }
                }
            }

            $reconciliationRun->update([
                'last_page_processed' => $page,
                'movies_created' => $this->moviesCreated,
                'candidates_created' => $this->candidatesCreated,
                'movies_reconciled' => $this->moviesReconciled,
            ]);

            $page++;
        } while (count($movies) === 100 && $page <= $lastPageToProcess);

        return $page - 1;
    }

    public function findByArchiveIdentifier(string $archiveIdentifier): bool
    {
        return Movie::where('archive_identifier', $archiveIdentifier)->exists();
    }
}
