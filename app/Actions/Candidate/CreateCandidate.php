<?php

namespace App\Actions\Candidate;

use App\Models\Candidate;
use App\Enums\CompatibilityLevel;
use App\DTOs\{TmdbMovieDto, };

class CreateCandidate
{
    public function execute(TmdbMovieDto $candidate, CompatibilityLevel $level, int $pendingMovieId): Candidate
    {
        return Candidate::create([
            'movie_id' => $pendingMovieId,
            'tmdb_id' => $candidate->id,
            'title' => $candidate->title,
            'overview' => $candidate->overview,
            'release_date' => (new \DateTime($candidate->releaseDate))->format('Y-m-d') ?? null,
            'level' => $level,
        ]);
    }
}