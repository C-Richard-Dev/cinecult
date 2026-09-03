<?php

namespace App\DTOs;

class TmdbMovieDto
{
    public function __construct(
        public int $id,
        public bool $adult,
        public ?string $backdropPath,
        public array $genreIds,
        public ?string $title,
        public ?string $originalLanguage,
        public ?string $originalTitle,
        public ?string $overview,
        public ?float $popularity,
        public ?string $posterPath,
        public ?string $releaseDate,
        public bool $video,
        public ?float $voteAverage,
        public ?int $voteCount
    ) {}
}
