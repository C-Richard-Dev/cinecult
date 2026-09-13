<?php

namespace App\Repositories;

use App\Models\ArchiveMovie;

class ArchiveRepository
{
    public function findByIdentifier(string $identifier): ?ArchiveMovie
    {
        return ArchiveMovie::where('identifier', $identifier)->first();
    }
}