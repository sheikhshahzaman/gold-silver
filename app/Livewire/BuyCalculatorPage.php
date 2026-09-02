<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Services\Rates\RatesProvider;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Buy Calculator - Islamabad Bullion Exchange')]
class BuyCalculatorPage extends Component
{
    public string $selectedMetal = 'gold';
    public string $selectedKarat = '24k';
    public string $selectedUnit = 'tola';
    public string $quantity = '1';
    public ?float $calculatedPrice = null;
    public ?float $unitPrice = null;
    public ?string $lastUpdated = null;
    public array $goldPrices = [];
    public array $silverPrices = [];
    public array $priceCatalog = [];
    public string $contactPhone = '';
    public string $contactWhatsapp = '';
    public string $contactAddress = '';

    private const GOLD_KARATS = [
        '24k' => '24K',
        'rawa' => 'Rawa',
        '22k' => '22K',
        '21k' => '21K',
        '18k' => '18K',
    ];

    private const GOLD_UNIT_OPTIONS = [
        'tola' => '1 Tola',
        'gram' => '1 Gram',
        '10_gram' => '10 Gram',
        'kg' => '1 KG',
    ];

    private const SILVER_UNIT_OPTIONS = [
        '10_tola_qr' => '10 Tola (QR Packaging)',
        '10_tola' => '10 Tola (999)',
        'kg' => '1 KG',
        '5_tola' => '5 Tola (Bar)',
        'tola' => '1 Tola (Bar)',
    ];

    public function mount(): void
    {
        $this->contactPhone = Setting::get('contact_phone', '');
        $this->contactWhatsapp = Setting::get('contact_whatsapp', '');
        $this->contactAddress = Setting::get('contact_address', '');
        $this->loadPrices();
        $this->calculatePrice();
    }

    public function selectMetal(string $metal): void
    {
        if (! in_array($metal, ['gold', 'silver'], true)) {
            return;
        }

        $this->selectedMetal = $metal;

        $this->calculatePrice();
    }

    public function selectKarat(string $karat): void
    {
        if (! array_key_exists($karat, $this->getGoldKaratsProperty())) {
            return;
        }

        $this->selectedKarat = $karat;
        $this->calculatePrice();
    }

    public function selectUnit(string $unit): void
    {
        $options = $this->getUnitOptionsProperty();

        if (! array_key_exists($unit, $options)) {
            return;
        }

        $this->selectedUnit = $unit;
        $this->calculatePrice();
    }

    public function updatedQuantity(): void
    {
        $this->quantity = preg_replace('/[^0-9.]/', '', $this->quantity) ?? '';
        $this->calculatePrice();
    }

    public function calculatePrice(): void
    {
        $this->loadPrices();

        $this->unitPrice = $this->getUnitBuyPrice();
        $quantity = $this->quantityValue();
        $this->calculatedPrice = ($this->unitPrice !== null && $quantity > 0)
            ? round($this->unitPrice * $quantity, 2)
            : null;
    }

    public function getGoldKaratsProperty(): array
    {
        $karats = $this->priceCatalog['gold']['karats'] ?? null;

        return is_array($karats) && $karats !== [] ? $karats : self::GOLD_KARATS;
    }

    public function getMetalsProperty(): array
    {
        $metals = $this->catalogSection('metals');

        return $metals !== [] ? $metals : ['gold' => 'Gold', 'silver' => 'Silver'];
    }

    public function getUnitOptionsProperty(): array
    {
        $units = $this->priceCatalog[$this->selectedMetal]['units'] ?? null;

        if (is_array($units) && $units !== []) {
            return $units;
        }

        return $this->selectedMetal === 'silver' ? self::SILVER_UNIT_OPTIONS : self::GOLD_UNIT_OPTIONS;
    }

    public function getSelectedLabelProperty(): string
    {
        $unit = $this->getUnitOptionsProperty()[$this->selectedUnit] ?? $this->selectedUnit;

        return $this->selectedMetal === 'gold'
            ? 'Gold '.($this->getGoldKaratsProperty()[$this->selectedKarat] ?? strtoupper($this->selectedKarat)).' - '.$unit
            : 'Silver - '.$unit;
    }

    public function getWhatsappUrlProperty(): ?string
    {
        $number = preg_replace('/[^0-9]/', '', $this->contactWhatsapp);

        if (! $number || $this->calculatedPrice === null) {
            return null;
        }

        $message = sprintf(
            'Hi, I calculated %s x %s = Rs %s. I want to buy.',
            $this->getSelectedLabelProperty(),
            $this->quantityValue(),
            number_format($this->calculatedPrice, 0),
        );

        return 'https://wa.me/'.$number.'?text='.urlencode($message);
    }

    private function loadPrices(): void
    {
        $allPrices = app(RatesProvider::class)->getAllPrices();

        $this->goldPrices = $allPrices['gold'] ?: [];
        $this->silverPrices = $allPrices['silver'] ?: [];
        $this->priceCatalog = is_array($allPrices['price_catalog'] ?? null) ? $allPrices['price_catalog'] : [];
        $this->lastUpdated = $allPrices['last_updated'] ?? null;
        $this->ensureValidSelections();
    }

    private function getUnitBuyPrice(): ?float
    {
        if ($this->selectedMetal === 'gold') {
            return $this->goldPrices[$this->selectedKarat][$this->selectedUnit]['buy'] ?? null;
        }

        return $this->silverPrices[$this->selectedUnit]['buy'] ?? null;
    }

    private function quantityValue(): float
    {
        $quantity = (float) $this->quantity;

        return $quantity > 0 ? min($quantity, 999999) : 0.0;
    }

    private function ensureValidSelections(): void
    {
        $metals = $this->catalogSection('metals');
        if ($metals && ! array_key_exists($this->selectedMetal, $metals)) {
            $this->selectedMetal = array_key_first($metals) ?: 'gold';
        }

        $karats = $this->getGoldKaratsProperty();
        if ($this->selectedMetal === 'gold' && $karats && ! array_key_exists($this->selectedKarat, $karats)) {
            $this->selectedKarat = array_key_first($karats) ?: '24k';
        }

        $units = $this->getUnitOptionsProperty();
        if ($units && ! array_key_exists($this->selectedUnit, $units)) {
            $this->selectedUnit = array_key_first($units) ?: 'tola';
        }
    }

    private function catalogSection(string $key): array
    {
        return is_array($this->priceCatalog[$key] ?? null) ? $this->priceCatalog[$key] : [];
    }

    public function render()
    {
        return view('livewire.buy-calculator-page');
    }
}
