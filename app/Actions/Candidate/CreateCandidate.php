<?php

namespace App\Actions\Candidate;

use App\DTOs\TmdbMovieDto;
use App\Enums\CompatibilityLevel;
use App\Models\Candidate;

class CreateCandidate
{
    public function execute(TmdbMovieDto $candidate, CompatibilityLevel $level, int $pendingMovieId): Candidate
    {
        return Candidate::create([
            'movie_id' => $pendingMovieId,
            'tmdb_id' => $candidate->id,
            'title' => $candidate->title,
            'overview' => $candidate->overview,
            'release_date' => $candidate->releaseDate !== null
                ? (new \DateTimeImmutable($candidate->releaseDate))->format('Y-m-d')
                : null,
            'level' => $level,
        ]);
    }
}
