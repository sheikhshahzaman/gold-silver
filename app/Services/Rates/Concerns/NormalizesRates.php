<?php

namespace App\Services\Rates\Concerns;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

trait NormalizesRates
{
    private function normalizeRates(array $prices): array
    {
        $prices['gold'] = is_array($prices['gold'] ?? null) ? $prices['gold'] : [];
        $prices['silver'] = is_array($prices['silver'] ?? null) ? $prices['silver'] : [];
        $prices['currencies'] = is_array($prices['currencies'] ?? null) ? $prices['currencies'] : [];
        $prices['international'] = is_array($prices['international'] ?? null) ? $prices['international'] : [];
        $prices['platinum'] = is_array($prices['platinum'] ?? null) ? $prices['platinum'] : [];
        $prices['palladium'] = is_array($prices['palladium'] ?? null) ? $prices['palladium'] : [];
        $prices['crude_oil'] = (float) ($prices['crude_oil'] ?? 0);
        $prices['psx'] = is_array($prices['psx'] ?? null) ? $prices['psx'] : [];
        $prices['price_catalog'] = is_array($prices['price_catalog'] ?? null) ? $prices['price_catalog'] : [];
        $prices['last_updated'] = $prices['last_updated'] ?? now()->toIso8601String();

        if (! isset($prices['quotes']) || ! is_array($prices['quotes'])) {
            $prices['quotes'] = $this->quotesFromInternational($prices);
        }

        if (! array_key_exists('live_rates_enabled', $prices)) {
            $prices['live_rates_enabled'] = Cache::remember(
                'setting.live_rates_enabled',
                60,
                fn () => Setting::get('live_rates_enabled', '1')
            ) === '1';
        }

        if (! array_key_exists('rate_mode', $prices)) {
            $prices['rate_mode'] = Cache::remember(
                'setting.rate_mode',
                60,
                fn () => Setting::get('rate_mode', 'manual')
            ) === 'live' ? 'live' : 'manual';
        }

        return $prices;
    }

    private function quotesFromInternational(array $prices): array
    {
        $goldSpreadPct = (float) Cache::remember(
            'setting.intl_spread_gold',
            60,
            fn () => Setting::get('international_spread_gold_pct', 0.05)
        );
        $silverSpreadPct = (float) Cache::remember(
            'setting.intl_spread_silver',
            60,
            fn () => Setting::get('international_spread_silver_pct', 0.1)
        );

        $goldBid = $prices['international']['xau_usd'] ?? null;
        $silverBid = $prices['international']['xag_usd'] ?? null;

        return [
            'gold' => $goldBid ? [
                'bid' => (float) $goldBid,
                'ask' => round((float) $goldBid * (1 + $goldSpreadPct / 100), 2),
            ] : null,
            'silver' => $silverBid ? [
                'bid' => (float) $silverBid,
                'ask' => round((float) $silverBid * (1 + $silverSpreadPct / 100), 2),
            ] : null,
        ];
    }
}
