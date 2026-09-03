<?php

namespace App\Repositories;

use App\Models\Movie;

class MovieRepository
{
    public function findByArchiveIdentifier(string $archiveIdentifier): bool
    {
        return Movie::where('archive_identifier', $archiveIdentifier)->exists();
    }
}
