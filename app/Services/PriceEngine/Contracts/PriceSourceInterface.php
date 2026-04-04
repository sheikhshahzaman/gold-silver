<?php

namespace App\Services\PriceEngine\Contracts;

interface PriceSourceInterface
{
    /**
     * Fetch price data from the source.
     *
     * @return array The fetched price data, or empty array on failure.
     */
    public function fetch(): array;

    /**
     * Get the name of this price source.
     */
    public function getSourceName(): string;

    /**
     * Check if this source is currently available and returning valid data.
     */
    public function isAvailable(): bool;
}
