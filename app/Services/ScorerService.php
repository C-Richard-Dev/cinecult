<?php

namespace App\Services;

use App\DTOs\{ArchiveMovieDto, CandidateScoreDto};
use App\Enums\CompatibilityLevel;

/**
 * Avalia candidatos do TMDB com base em:
 *
 * - Título
 * - Ano de lançamento
 * - Descrição
 *
 * A pontuação final varia de 0 a 100.
 */
class ScorerService
{
    public function score(ArchiveMovieDto $movie, array $candidates): array
    {
        $candidatesScored = [];

        foreach ($candidates as $candidate) {
            $titleScore = $this->compareTitle(
                $movie->title,
                $candidate->title
            );

            $yearScore = $this->compareYear(
                $movie->year,
                $candidate->releaseDate
            );

            $descriptionScore = $this->compareDescription(
                $movie->description,
                $candidate->overview
            );

            $score = $titleScore + $yearScore + $descriptionScore;
            $compatibilityLevel = $this->getCompatibilityLevel($score);

            $candidatesScored[] = new CandidateScoreDto(
                candidate: $candidate,
                level: $compatibilityLevel,
                score: $score
            );
        }

        usort($candidatesScored, fn (CandidateScoreDto $a, CandidateScoreDto $b) => $b->score <=> $a->score);

        return $candidatesScored;
    }

    /**
     * Compara os títulos.
     *
     * Retorna de 0 a 50 pontos.
     */
    private function compareTitle(string $titleA, string $titleB): int
    {
        $titleA = $this->normalizeText($titleA);
        $titleB = $this->normalizeText($titleB);

        if ($titleA === '' || $titleB === '') {
            return 0;
        }

        if ($titleA === $titleB) {
            return 50;
        }

        similar_text($titleA, $titleB, $percentage);

        return (int) round(($percentage / 100) * 50);
    }

    /**
     * Compara o ano do Archive com a data de lançamento do TMDB.
     *
     * Retorna de 0 a 30 pontos.
     */
    private function compareYear(?int $archiveYear, ?string $tmdbReleaseDate): int
    {
        if (!$archiveYear || !$tmdbReleaseDate) {
            return 0;
        }

        $tmdbYear = (int) substr($tmdbReleaseDate, 0, 4);

        return $archiveYear === $tmdbYear ? 30 : 0;
    }

    /**
     * Compara as descrições.
     *
     * Retorna de 0 a 20 pontos.
     */
    private function compareDescription(
        ?string $descriptionA,
        ?string $descriptionB
    ): int {
        if (!$descriptionA || !$descriptionB) {
            return 0;
        }

        $descriptionA = $this->normalizeText($descriptionA);
        $descriptionB = $this->normalizeText($descriptionB);

        if ($descriptionA === $descriptionB) {
            return 20;
        }

        similar_text($descriptionA, $descriptionB, $percentage);

        return (int) round(($percentage / 100) * 20);
    }

    /**
     * Normaliza o texto antes da comparação.
     */
    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);

        $text = iconv(
            'UTF-8',
            'ASCII//TRANSLIT//IGNORE',
            $text
        ) ?: $text;

        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    private function getCompatibilityLevel(int $score): CompatibilityLevel
    {
        return match (true) {
            $score >= 100 => CompatibilityLevel::HIGH,
            $score >= 80 => CompatibilityLevel::GOOD,
            $score >= 60 => CompatibilityLevel::MEDIUM,
            default => CompatibilityLevel::LOW,
        };
    }
}