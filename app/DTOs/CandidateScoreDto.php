<?php

namespace App\DTOs;

use App\DTOs\TmdbMovieDto;
use App\Enums\CompatibilityLevel;

class CandidateScoreDto
{
    public function __construct(
        public TmdbMovieDto $candidate,
        public CompatibilityLevel $level,
        public int $score,
    ) {}
}