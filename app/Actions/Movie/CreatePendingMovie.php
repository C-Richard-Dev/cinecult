<?php

namespace App\Actions\Movie;

use App\Models\Movie;
use App\DTOs\ArchiveMovieDto;
use App\Enums\MovieConciliationStatus; 

class CreatePendingMovie
{
    public function execute(ArchiveMovieDto $movie): Movie
    {
        return Movie::create([
            'archive_identifier' => $movie->identifier,
            'title' => $movie->title,
            'conciliation_status' => MovieConciliationStatus::PENDING,
        ]);
    }
}