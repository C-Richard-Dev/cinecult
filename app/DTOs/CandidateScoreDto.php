<?php

namespace App\DTOs;

use App\DTOs\TmdbMovieDto;

class CandidateScoreDto
{
    public function __construct(
        public TmdbMovieDto $candidate,
        public int $score,
    ) {}
}