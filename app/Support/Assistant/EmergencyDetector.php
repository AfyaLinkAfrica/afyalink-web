<?php

namespace App\Support\Assistant;

/**
 * Spots wording that may describe an emergency, so the assistant can say, first of all,
 * to contact emergency services or go to the nearest facility.
 *
 * The list is deliberately broad: showing the notice by mistake costs nothing,
 * while missing a real emergency does. A healthcare reviewer should check this list.
 */
class EmergencyDetector
{
    private const PHRASES = [
        'emergency', 'dharura',
        "can't breathe", 'cannot breathe', 'can not breathe', 'difficulty breathing', 'trouble breathing',
        'not breathing', 'stopped breathing', 'choking',
        'chest pain', 'heart attack', 'stroke',
        'heavy bleeding', 'bleeding heavily', 'bleeding a lot', "won't stop bleeding",
        'unconscious', 'passed out', 'collapsed',
        'seizure', 'convulsion', 'convulsions',
        'overdose', 'poison', 'poisoned', 'poisoning', 'snake bite', 'snakebite',
        'severe pain',
        'suicide', 'kill myself', 'want to die',
    ];

    public function matches(string $message): bool
    {
        $text = TextNormalizer::normalize(mb_substr($message, 0, 2000));

        foreach (self::PHRASES as $phrase) {
            if (TextNormalizer::contains($text, $phrase)) {
                return true;
            }
        }

        return false;
    }
}
