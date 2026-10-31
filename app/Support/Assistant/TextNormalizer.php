<?php

namespace App\Support\Assistant;

/**
 * Makes visitor text and keyword lists comparable: lower case, no apostrophes,
 * and every run of punctuation or spaces reduced to a single space.
 */
final class TextNormalizer
{
    public static function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = str_replace(["'", '’', '`'], '', $text);

        return trim(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text) ?? '');
    }

    /**
     * Whole-word phrase match. $normalizedText must already be normalized.
     */
    public static function contains(string $normalizedText, string $phrase): bool
    {
        return str_contains(' '.$normalizedText.' ', ' '.self::normalize($phrase).' ');
    }
}
