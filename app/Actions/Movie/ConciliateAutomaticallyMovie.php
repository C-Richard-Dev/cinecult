<?php

namespace App\Actions\Movie;

use App\DTOs\ArchiveMovieDto;
use App\DTOs\TmdbMovieDto;
use App\Models\Movie;
use App\Actions\Movie\SyncCategoriesToMovie;
use App\Enums\ConciliationStatus;

class ConciliateAutomaticallyMovie
{
    private SyncCategoriesToMovie $syncCategoriesToMovie;

    public function __construct(SyncCategoriesToMovie $syncCategoriesToMovie)
    {
        $this->syncCategoriesToMovie = $syncCategoriesToMovie;
    }

    public function execute(
        int $movieId,
        ArchiveMovieDto $archiveMovie,
        TmdbMovieDto $tmdbMovie
    ): Movie {
        $movie = Movie::findOrFail($movieId);
        $movie->update([
                'tmdb_id' => $tmdbMovie->id,
                'conciliation_status' => ConciliationStatus::AUTOMATIC,
                'original_title' => $tmdbMovie->originalTitle,
                'overview' => $tmdbMovie->overview,
                'release_date' => $tmdbMovie->releaseDate,
                'poster_path' => $tmdbMovie->posterPath,
                'backdrop_path' => $tmdbMovie->backdropPath,
                'video_file_name' => $archiveMovie->videoFileName,
                'conciliation_date' => now(),
            ]);

        $this->syncCategoriesToMovie->execute($movie, $tmdbMovie->genreIds);

        return $movie;
    }
}
