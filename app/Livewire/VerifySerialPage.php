<?php

namespace App\Livewire;

use App\Models\InventoryItem;
use App\Models\SerialVerificationAttempt;
use App\Models\VerifiedSerial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Verify Serial Number - Islamabad Bullion Exchange')]
class VerifySerialPage extends Component
{
    public string $serialNumber = '';
    public string $customerName = '';
    public string $customerPhone = '';

    public ?InventoryItem $foundItem = null;
    public ?VerifiedSerial $foundSerial = null;
    public bool $submitted = false;
    public bool $isValid = false;
    public string $errorMessage = '';

    public function verify(): void
    {
        $this->validate([
            'serialNumber' => 'required|string|min:5|max:80',
            'customerName' => 'nullable|string|max:100',
            'customerPhone' => 'nullable|string|max:30',
        ]);

        $normalized = strtoupper(trim($this->serialNumber));

        // Stored serials carry the IBE- prefix; accept entries without it too.
        $candidates = array_unique([
            $normalized,
            str_starts_with($normalized, 'IBE-') ? $normalized : 'IBE-'.$normalized,
        ]);

        $item = InventoryItem::with('product.productCategory')
            ->whereIn('serial_number', $candidates)
            ->first();

        $approvedSerial = null;
        if (!$item) {
            $approvedSerial = VerifiedSerial::whereIn('serial_number', $candidates)
                ->where('is_active', true)
                ->first();
        }

        $statusAtLookup = $item?->status ?? ($approvedSerial ? 'approved' : null);

        SerialVerificationAttempt::create([
            'entered_serial' => $normalized,
            'inventory_item_id' => $item?->id,
            'found' => (bool) ($item || $approvedSerial),
            'status_at_lookup' => $statusAtLookup,
            'customer_name' => $this->customerName ?: null,
            'customer_phone' => $this->customerPhone ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'attempted_at' => now(),
        ]);

        $this->submitted = true;

        if ($item) {
            $this->foundItem = $item;
            $this->isValid = true;
            $item->logScan(request()->ip(), request()->userAgent());
        } elseif ($approvedSerial) {
            $this->foundSerial = $approvedSerial;
            $this->isValid = true;
        } else {
            $this->isValid = false;
            $this->errorMessage = 'Serial number not found in our records. This piece may not be from Islamabad Bullion Exchange.';
        }
    }

    public function reset_form(): void
    {
        $this->reset(['serialNumber', 'customerName', 'customerPhone', 'foundItem', 'foundSerial', 'submitted', 'isValid', 'errorMessage']);
    }

    public function render()
    {
        return view('livewire.verify-serial-page');
    }
}
