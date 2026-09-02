<?php

namespace App\Services\Rates;

interface RatesProvider
{
    public function getAllPrices(): array;
}
