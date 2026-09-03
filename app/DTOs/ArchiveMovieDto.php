<?php

namespace App\DTOs;

class ArchiveMovieDto
{
    public function __construct(
        public string $identifier,
        public string $title,
        public ?string $description,
        public ?int $year,
        public ?string $date,
        public ?string $language,
        public ?string $creator,
        public ?array $subject,
    ) {}
    
}