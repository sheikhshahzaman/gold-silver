<?php

namespace App\Services\Rates;

use App\Services\PriceEngine\PriceCacheManager;
use App\Services\Rates\Concerns\NormalizesRates;

class LocalRatesProvider implements RatesProvider
{
    use NormalizesRates;

    public function __construct(
        private readonly PriceCacheManager $cacheManager,
        private readonly PriceCatalogBuilder $catalogBuilder,
    )
    {
    }

    public function getAllPrices(): array
    {
        $prices = $this->normalizeRates($this->cacheManager->getAllPrices());

        if (empty($prices['price_catalog'])) {
            $prices['price_catalog'] = $this->catalogBuilder->build($prices['gold']);
        }

        return $this->normalizeRates($prices);
    }
}
