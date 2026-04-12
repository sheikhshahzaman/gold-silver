<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Checkout - Islamabad Bullion Exchange')]
class CheckoutPage extends Component
{
    use WithFileUploads;

    public Order $order;
    public int $step = 1;

    // Step 1: Customer info
    public string $customerName = '';
    public string $customerPhone = '';

    // Step 2: Payment method
    public string $paymentMethod = '';

    // Step 3: Proof upload
    public $proofImage = null;
    public string $referenceNumber = '';

    // Payment account details from settings
    public array $paymentAccounts = [];

    public function mount(string $orderNumber): void
    {
        $this->order = Order::where('order_number', $orderNumber)
            ->whereNull('customer_name')
            ->where('status', 'pending')
            ->whereDoesntHave('payment')
            ->firstOrFail();

        $this->loadPaymentAccounts();
    }

    public function goToStep2(): void
    {
        $this->validate([
            'customerName' => 'required|string|min:2|max:255',
            'customerPhone' => 'required|string|min:10|max:20',
        ], [
            'customerName.required' => 'Please enter your name.',
            'customerPhone.required' => 'Please enter your phone number.',
            'customerPhone.min' => 'Please enter a valid phone number.',
        ]);

        $this->order->update([
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
        ]);

        $this->step = 2;
    }

    public function selectPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
    }

    public function goToStep3(): void
    {
        $this->validate([
            'paymentMethod' => 'required|in:easypaisa,jazzcash,raast,bank_transfer',
        ], [
            'paymentMethod.required' => 'Please select a payment method.',
        ]);

        $this->step = 3;
    }

    public function goBackToStep1(): void
    {
        $this->step = 1;
    }

    public function goBackToStep2(): void
    {
        $this->step = 2;
    }

    /**
     * Validate the proof image immediately after upload.
     */
    public function updatedProofImage(): void
    {
        $this->validateOnly('proofImage', [
            'proofImage' => 'image|max:5120',
        ], [
            'proofImage.image' => 'The file must be an image (JPG, PNG, etc.).',
            'proofImage.max' => 'The image must not be larger than 5MB.',
        ]);
    }

    public function submitPayment(): void
    {
        $this->validate([
            'proofImage' => 'required|image|max:5120',
            'referenceNumber' => 'nullable|string|max:255',
        ], [
            'proofImage.required' => 'Please upload your payment screenshot.',
            'proofImage.image' => 'The file must be an image.',
            'proofImage.max' => 'The image must not be larger than 5MB.',
        ]);

        $path = $this->proofImage->store('payment-proofs', 'public');

        Payment::create([
            'order_id' => $this->order->id,
            'method' => $this->paymentMethod,
            'amount' => $this->order->total_amount,
            'proof_image' => $path,
            'reference_number' => $this->referenceNumber ?: null,
            'status' => 'pending',
        ]);

        $this->order->update(['status' => 'awaiting_verification']);

        redirect()->route('order.show', $this->order->order_number);
    }

    private function loadPaymentAccounts(): void
    {
        $this->paymentAccounts = [
            'easypaisa' => [
                'number' => Setting::get('payment_easypaisa_number', ''),
                'name' => Setting::get('payment_easypaisa_name', ''),
            ],
            'jazzcash' => [
                'number' => Setting::get('payment_jazzcash_number', ''),
                'name' => Setting::get('payment_jazzcash_name', ''),
            ],
            'raast' => [
                'id' => Setting::get('payment_raast_id', ''),
                'name' => Setting::get('payment_raast_name', ''),
            ],
            'bank_transfer' => [
                'bank_name' => Setting::get('payment_bank_name', ''),
                'account_title' => Setting::get('payment_bank_account_title', ''),
                'account_number' => Setting::get('payment_bank_account_number', ''),
                'iban' => Setting::get('payment_bank_iban', ''),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.checkout-page');
    }
}
