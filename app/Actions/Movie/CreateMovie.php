<?php

namespace App\Actions\Movie;

use App\DTOs\ArchiveMovieDto;
use App\DTOs\TmdbMovieDto;
use App\Models\Movie;

class CreateMovie
{
    public function execute(
        ArchiveMovieDto $archiveMovie,
        TmdbMovieDto $tmdbMovie
    ): Movie {

        $category = $this->resolveCategory($tmdbMovie);

        return Movie::create([
            'category' => 1,

            'archive_identifier' => $archiveMovie->identifier,
            'tmdb_id' => $tmdbMovie->id,

            'title' => $tmdbMovie->title,
            'original_title' => $tmdbMovie->originalTitle,
            'overview' => $tmdbMovie->overview,

            'release_date' => $tmdbMovie->releaseDate,

            'poster_path' => $tmdbMovie->posterPath,
            'backdrop_path' => $tmdbMovie->backdropPath,

            'video_file_name' => $archiveMovie->videoFileName,
        ]);
    }
}
