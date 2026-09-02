<?php

namespace App\Filament\Pages;

use App\Support\StaffAccess;
use App\Services\Rates\RatesProvider;
use Filament\Pages\Page;

class MetalPrices extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string | \UnitEnum | null $navigationGroup = 'Price Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Metal Prices';

    protected static ?string $title = 'Metal Prices';

    protected string $view = 'filament.pages.metal-prices';

    public array $prices = [];

    public static function shouldRegisterNavigation(): bool
    {
        return ! class_exists(\App\Filament\Resources\MetalPriceResource::class)
            && StaffAccess::can('metal_prices', StaffAccess::ACTION_VIEW);
    }

    public static function canAccess(): bool
    {
        return StaffAccess::can('metal_prices', StaffAccess::ACTION_VIEW);
    }

    public function mount(): void
    {
        $this->refreshRates();
    }

    public function refreshRates(): void
    {
        $this->prices = app(RatesProvider::class)->getAllPrices();
    }

    public function goldQuote(): ?array
    {
        return $this->prices['quotes']['gold'] ?? null;
    }

    public function silverQuote(): ?array
    {
        return $this->prices['quotes']['silver'] ?? null;
    }

    public function currency(string $key): ?array
    {
        return $this->prices['currencies'][$key] ?? null;
    }

    public function rows(): array
    {
        $gold = $this->goldQuote();
        $silver = $this->silverQuote();

        $rows = [
            [
                'item' => 'Gold (XAU)',
                'type' => 'International Spot',
                'unit' => 'Per oz',
                'buy' => $gold['bid'] ?? ($this->prices['international']['xau_usd'] ?? null),
                'sell' => $gold['ask'] ?? ($this->prices['international']['xau_usd'] ?? null),
                'currency' => 'USD',
                'source' => 'Old live rates API',
                'updated_at' => $this->prices['last_updated'] ?? null,
            ],
            [
                'item' => 'Silver (XAG)',
                'type' => 'International Spot',
                'unit' => 'Per oz',
                'buy' => $silver['bid'] ?? ($this->prices['international']['xag_usd'] ?? null),
                'sell' => $silver['ask'] ?? ($this->prices['international']['xag_usd'] ?? null),
                'currency' => 'USD',
                'source' => 'Old live rates API',
                'updated_at' => $this->prices['last_updated'] ?? null,
            ],
        ];

        foreach ([
            'usd_pkr' => 'USD/PKR',
            'usd_interbank' => 'USD Interbank',
            'gbp_pkr' => 'GBP/PKR',
            'eur_pkr' => 'EUR/PKR',
            'sar_pkr' => 'SAR/PKR',
            'aed_pkr' => 'AED/PKR',
            'myr_pkr' => 'MYR/PKR',
        ] as $key => $label) {
            $rate = $this->currency($key);

            if (! $rate) {
                continue;
            }

            $rows[] = [
                'item' => $label,
                'type' => 'Currency',
                'unit' => 'Pair',
                'buy' => $rate['buy'] ?? null,
                'sell' => $rate['sell'] ?? null,
                'currency' => 'PKR',
                'source' => 'Old live rates API',
                'updated_at' => $this->prices['last_updated'] ?? null,
            ];
        }

        return $rows;
    }
}
