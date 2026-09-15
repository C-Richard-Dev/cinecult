<?php

use App\Actions\Candidate\CreateCandidate;
use App\DTOs\TmdbMovieDto;
use App\Enums\CompatibilityLevel;
use App\Models\Candidate;
use App\Models\Movie;

test('creates a candidate with a nullable release date and compatibility level', function () {
    $movie = Movie::create([
        'archive_identifier' => 'archive-movie',
        'title' => 'Archive Movie',
    ]);

    $candidate = app(CreateCandidate::class)->execute(
        new TmdbMovieDto(
            id: 123,
            adult: false,
            backdropPath: null,
            genreIds: [],
            title: 'TMDB Movie',
            originalLanguage: 'en',
            originalTitle: 'TMDB Movie',
            overview: null,
            popularity: null,
            posterPath: null,
            releaseDate: null,
            video: false,
            voteAverage: null,
            voteCount: null,
        ),
        CompatibilityLevel::GOOD,
        $movie->id,
    );

    expect($candidate)
        ->toBeInstanceOf(Candidate::class)
        ->and($candidate->movie_id)->toBe($movie->id)
        ->and($candidate->release_date)->toBeNull()
        ->and($candidate->level)->toBe(CompatibilityLevel::GOOD);
});
