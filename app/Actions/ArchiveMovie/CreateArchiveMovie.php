<?php

namespace App\Actions\ArchiveMovie;

use App\DTOs\ArchiveMovieDto;
use App\Models\ArchiveMovie;

class CreateArchiveMovie
{
    public function execute(ArchiveMovieDto $movie): ArchiveMovie
    {
        return ArchiveMovie::firstOrCreate([
            'identifier' => $movie->identifier,
        ], [
            'title' => $movie->title,
            'description' => $movie->description,
            'year' => $movie->year,
            'date' => $movie->date,
            'language' => $movie->language,
            'creator' => $movie->creator,
        ]);
    }
}