<?php

namespace App\Services\PriceEngine;

class PriceCalculator
{
    /**
     * Weight of one tola in grams.
     */
    public const TOLA_IN_GRAMS = 11.6638;

    /**
     * Weight of one troy ounce in grams.
     */
    public const TROY_OUNCE_IN_GRAMS = 31.1035;

    /**
     * Karat purity multipliers relative to 24K.
     */
    private const KARAT_MULTIPLIERS = [
        '24k' => 1.0,
        '22k' => 0.9167,
        '21k' => 0.875,
        '18k' => 0.75,
    ];

    /**
     * Rawa purity (slightly below 24k fine gold).
     */
    private const RAWA_MULTIPLIER = 0.995;

    /**
     * Convert international gold/silver price per troy ounce (USD) to PKR per tola.
     */
    public function ounceToPkrPerTola(float $pricePerOunceUsd, float $usdToPkr): float
    {
        // 1 troy ounce = 31.1035g, 1 tola = 11.6638g
        // Price per gram in USD = pricePerOunce / 31.1035
        // Price per tola in USD = (pricePerOunce / 31.1035) * 11.6638
        // Price per tola in PKR = above * usdToPkr
        $pricePerGramUsd = $pricePerOunceUsd / self::TROY_OUNCE_IN_GRAMS;
        $pricePerTolaUsd = $pricePerGramUsd * self::TOLA_IN_GRAMS;

        return round($pricePerTolaUsd * $usdToPkr, 2);
    }

    /**
     * Convert international gold/silver price per troy ounce (USD) to PKR per gram.
     */
    public function ounceToPkrPerGram(float $pricePerOunceUsd, float $usdToPkr): float
    {
        $pricePerGramUsd = $pricePerOunceUsd / self::TROY_OUNCE_IN_GRAMS;

        return round($pricePerGramUsd * $usdToPkr, 2);
    }

    /**
     * Given a price per tola, derive prices for all standard units.
     *
     * @return array{tola: float, 10_tola: float, 10_gram: float, 5_gram: float, gram: float, kg: float, ounce: float}
     */
    public function deriveAllUnitPrices(float $perTolaPrice): array
    {
        $pricePerGram = $perTolaPrice / self::TOLA_IN_GRAMS;

        return [
            'tola' => round($perTolaPrice, 2),
            '10_tola' => round($perTolaPrice * 10, 2),
            '10_gram' => round($pricePerGram * 10, 2),
            '5_gram' => round($pricePerGram * 5, 2),
            'gram' => round($pricePerGram, 2),
            'kg' => round($pricePerGram * 1000, 2),
            'ounce' => round($pricePerGram * self::TROY_OUNCE_IN_GRAMS, 2),
        ];
    }

    /**
     * Apply karat purity to a 24K price.
     *
     * @param float  $price24k The 24K pure gold price
     * @param string $karat    One of: 24k, rawa, 22k, 21k, 18k
     */
    public function applyKaratPurity(float $price24k, string $karat): float
    {
        $karat = strtolower($karat);

        if ($karat === 'rawa') {
            return round($price24k * self::RAWA_MULTIPLIER, 2);
        }

        $multiplier = self::KARAT_MULTIPLIERS[$karat] ?? 1.0;

        return round($price24k * $multiplier, 2);
    }

    /**
     * Apply a per-tola margin proportionally to the given unit and add it to the base price.
     *
     * @param float  $basePrice      The base price in the target unit
     * @param float  $marginPerTola  The margin amount in PKR per tola
     * @param string $unit           The unit of the base price (tola, gram, 10_gram, etc.)
     */
    public function applyMargin(float $basePrice, float $marginPerTola, string $unit): float
    {
        $marginForUnit = $this->convertMarginToUnit($marginPerTola, $unit);

        return round($basePrice + $marginForUnit, 2);
    }

    /**
     * Convert a per-tola margin to the equivalent margin for a different unit.
     */
    private function convertMarginToUnit(float $marginPerTola, string $unit): float
    {
        $marginPerGram = $marginPerTola / self::TOLA_IN_GRAMS;

        return match ($unit) {
            'tola' => $marginPerTola,
            '10_tola' => $marginPerTola * 10,
            '10_gram' => $marginPerGram * 10,
            '5_gram' => $marginPerGram * 5,
            'gram' => $marginPerGram,
            'kg' => $marginPerGram * 1000,
            'ounce' => $marginPerGram * self::TROY_OUNCE_IN_GRAMS,
            default => $marginPerTola,
        };
    }

    /**
     * Given a 24K price per tola, derive prices for all karats.
     *
     * @return array{24k: float, rawa: float, 22k: float, 21k: float, 18k: float}
     */
    public function deriveAllKaratPrices(float $price24kPerTola): array
    {
        return [
            '24k' => round($price24kPerTola, 2),
            'rawa' => round($price24kPerTola * self::RAWA_MULTIPLIER, 2),
            '22k' => round($price24kPerTola * self::KARAT_MULTIPLIERS['22k'], 2),
            '21k' => round($price24kPerTola * self::KARAT_MULTIPLIERS['21k'], 2),
            '18k' => round($price24kPerTola * self::KARAT_MULTIPLIERS['18k'], 2),
        ];
    }
}
