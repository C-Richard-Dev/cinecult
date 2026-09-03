<?php

use App\Enums\MovieCategory;

test('movie category enum contains the expected TMDB values', function () {
    expect(MovieCategory::Action->value)->toBe(28)
        ->and(MovieCategory::Adventure->value)->toBe(12)
        ->and(MovieCategory::Animation->value)->toBe(16)
        ->and(MovieCategory::Comedy->value)->toBe(35)
        ->and(MovieCategory::Crime->value)->toBe(80)
        ->and(MovieCategory::Documentary->value)->toBe(99)
        ->and(MovieCategory::Drama->value)->toBe(18)
        ->and(MovieCategory::Family->value)->toBe(10751)
        ->and(MovieCategory::Fantasy->value)->toBe(14)
        ->and(MovieCategory::History->value)->toBe(36)
        ->and(MovieCategory::Horror->value)->toBe(27)
        ->and(MovieCategory::Music->value)->toBe(10402)
        ->and(MovieCategory::Mystery->value)->toBe(9648)
        ->and(MovieCategory::Romance->value)->toBe(10749)
        ->and(MovieCategory::ScienceFiction->value)->toBe(878)
        ->and(MovieCategory::TVMovie->value)->toBe(10770)
        ->and(MovieCategory::Thriller->value)->toBe(53)
        ->and(MovieCategory::War->value)->toBe(10752)
        ->and(MovieCategory::Western->value)->toBe(37);
});
