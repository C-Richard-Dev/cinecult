<?php

namespace App\Actions\ArchiveMovie;

use App\Models\ArchiveMovie;

class CreateArchiveMovie
{
    public function execute(array $data): ArchiveMovie
    {
        return ArchiveMovie::create($data);
    }
}