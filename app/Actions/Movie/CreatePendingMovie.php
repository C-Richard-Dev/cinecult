<?php

namespace App\Actions\Movie;

use App\DTOs\ArchiveMovieDto;
use App\Enums\MovieReconciliationStatus;
use App\Models\Movie;

class CreatePendingMovie
{
    public function execute(ArchiveMovieDto $movie): Movie
    {
        return Movie::create([
            'archive_identifier' => $movie->identifier,
            'title' => $movie->title,
            'reconciliation_status' => MovieReconciliationStatus::PENDING,
        ]);
    }
}
