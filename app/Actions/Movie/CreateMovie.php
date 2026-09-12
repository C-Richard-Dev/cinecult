<?php

namespace App\Actions\Movie;

use App\DTOs\ArchiveMovieDto;
use App\DTOs\TmdbMovieDto;
use App\Models\Movie;
use App\Actions\Movie\SyncCategoriesToMovie;

class CreateMovie
{
    private SyncCategoriesToMovie $syncCategoriesToMovie;

    public function __construct(SyncCategoriesToMovie $syncCategoriesToMovie)
    {
        $this->syncCategoriesToMovie = $syncCategoriesToMovie;
    }

    public function execute(
        ArchiveMovieDto $archiveMovie,
        TmdbMovieDto $tmdbMovie
    ): Movie {

        $movie = Movie::create([
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

        $this->syncCategoriesToMovie->execute($movie, $tmdbMovie->genreIds);

        return $movie;
    }
}
