<?php

namespace App\Services\BuyRequests;

use App\Models\BuyRequest;
use App\Models\Product;
use App\Models\Setting;
use App\Services\Cart;
use App\Services\Rates\RatesProvider;

/**
 * Works out what a buy request costs.
 *
 * The client never sends a price. Bars are priced from the admin product, rawa
 * from the admin-managed "Gold - RAWA" board rows, so a tampered request cannot
 * quote itself a cheaper number.
 */
class BuyRequestPricing
{
    /** Matches PricesApi in the app and Cart in the website. */
    public const GRAMS_PER_TOLA = 11.6638038;

    public function __construct(
        private readonly RatesProvider $rates,
        private readonly Cart $cart,
    ) {}

    /**
     * @return array{unit_price: float, packaging_charge: float, total_amount: float}
     *
     * @throws BuyRequestPricingException
     */
    public function priceBar(Product $product): array
    {
        $unit = $this->cart->unitPriceFor($product);

        if ($unit === null || $unit <= 0) {
            throw new BuyRequestPricingException(
                "Price for {$product->name} is currently unavailable. Please try again shortly."
            );
        }

        $packaging = (float) $product->packaging_charge;

        return [
            'unit_price' => round($unit, 2),
            'packaging_charge' => round($packaging, 2),
            'total_amount' => round($unit + $packaging, 2),
        ];
    }

    /**
     * Rawa is loose gold, so the customer names a weight rather than picking a
     * product. The per-unit rate comes straight from the admin board; only if a
     * unit row is missing do we derive it from the tola rate by gram weight.
     *
     * @throws BuyRequestPricingException
     */
    public function priceRawa(float $weight, string $unit): array
    {
        if ($weight <= 0) {
            throw new BuyRequestPricingException('Please enter a weight greater than zero.');
        }

        $rate = $this->rawaRateFor($unit);

        if ($rate === null || $rate <= 0) {
            throw new BuyRequestPricingException(
                'Rawa rates are currently unavailable. Please try again shortly.'
            );
        }

        $total = $weight * $rate;
        $packaging = (float) Setting::get('rawa_packaging_charge', 0);

        return [
            'unit_price' => round($rate, 2),
            'packaging_charge' => round($packaging, 2),
            'total_amount' => round($total + $packaging, 2),
        ];
    }

    /** Per-gram or per-tola rawa rate on the buy side (what the customer pays). */
    private function rawaRateFor(string $unit): ?float
    {
        $rawa = data_get($this->rates->getAllPrices(), 'gold.rawa', []);

        $perTola = $this->positive(data_get($rawa, 'tola.buy'));
        $perGram = $this->positive(data_get($rawa, 'gram.buy'));

        if ($unit === BuyRequest::UNIT_TOLA) {
            // Prefer the admin's own tola row; fall back to gram x tola weight.
            return $perTola ?? ($perGram !== null ? $perGram * self::GRAMS_PER_TOLA : null);
        }

        return $perGram ?? ($perTola !== null ? $perTola / self::GRAMS_PER_TOLA : null);
    }

    private function positive(mixed $value): ?float
    {
        return (is_numeric($value) && (float) $value > 0) ? (float) $value : null;
    }
}
