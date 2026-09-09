<?php

namespace App\Services;

use App\DTOs\{ArchiveMovieDto, CandidateScoredDto};
use Illuminate\Support\Facades\Http;

class ScorerService
{

    public function score(ArchiveMovieDto $movie, array $candidates): ?array
    {
        $candidatesScored = [];

        foreach ($candidates as $candidate) {
            $score = 0;

            $titleScore = $this->compareTitle($movie->title, $candidate->title);
            $yearScore = $this->compareYear($movie->year, $candidate->year);
            $descriptionScore = $this->compareDescription($movie->description, $candidate->overview);

            $score = $titleScore + $yearScore + $descriptionScore;

            $candidateScored = new CandidateScoredDto(
                candidate: $candidate,
                score: $score
            );

            // adiciona o objeto CandidateScoredDto ao array de candidatos pontuados
            $candidatesScored[] = $candidateScored;
        }

        // ordena o array de candidatos do maior para o menor score
        usort($candidatesScored, fn (CandidateScoredDto $a, CandidateScoredDto $b) => $b->score <=> $a->score);

        return $candidatesScored;
    }
}