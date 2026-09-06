<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\Orders\OrderNotificationService;
use App\Services\Orders\OrderPricingService;
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

    // Step 2: Payment method + delivery
    // Only bank transfer is offered, so it's pre-selected — nothing to choose.
    public string $paymentMethod = 'bank_transfer';
    public string $deliveryMethod = 'pickup';
    public string $deliveryAddress = '';
    public float $deliveryCharge = 0;

    // Step 3: Proof upload
    public $proofImage = null;
    public string $referenceNumber = '';

    // Payment account details from settings
    public array $paymentAccounts = [];

    public function mount(string $orderNumber): void
    {
        $this->order = Order::with(['items.product', 'payment'])->where('order_number', $orderNumber)
            ->whereNull('customer_name')
            ->where('status', 'pending')
            ->whereDoesntHave('payment')
            ->firstOrFail();

        $this->refreshOrderPricing();

        $this->loadPaymentAccounts();
        $this->deliveryCharge = (float) Setting::get('delivery_charge', 0);
    }

    public function selectDeliveryMethod(string $method): void
    {
        $this->deliveryMethod = $method;

        // Cash is pickup-only and COD is delivery-only, so switching delivery
        // can invalidate the chosen payment method.
        if (! Payment::isValidForDelivery($this->paymentMethod, $this->deliveryMethod)) {
            $this->paymentMethod = Payment::METHOD_BANK_TRANSFER;
        }
    }

    /** Payment methods offered for the current delivery choice. */
    public function availablePaymentMethods(): array
    {
        return Payment::methodsForDelivery($this->deliveryMethod);
    }

    public function paymentMethodLabel(string $method): string
    {
        return Payment::methodOptions()[$method] ?? $method;
    }

    /** Only a bank transfer sends the customer to the screenshot step. */
    public function needsProof(): bool
    {
        return Payment::requiresProof($this->paymentMethod);
    }

    /** Total the customer actually pays: product total + delivery (0 for pickup). */
    public function grandTotal(): float
    {
        $delivery = $this->deliveryMethod === 'delivery' ? $this->deliveryCharge : 0;

        return (float) $this->order->total_amount + $delivery;
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

        $this->refreshOrderPricing();

        $this->step = 2;
    }

    public function selectPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
    }

    public function goToStep3(): void
    {
        $this->validate([
            'paymentMethod' => 'required|in:'.implode(',', array_keys(Payment::methodOptions())),
            'deliveryMethod' => 'required|in:pickup,delivery',
            'deliveryAddress' => 'required_if:deliveryMethod,delivery|nullable|string|max:1000',
        ], [
            'paymentMethod.required' => 'Please select a payment method.',
            'deliveryAddress.required_if' => 'Please enter your delivery address.',
        ]);

        if (! Payment::isValidForDelivery($this->paymentMethod, $this->deliveryMethod)) {
            $this->addError('paymentMethod', 'That payment method is not available for this delivery option.');

            return;
        }

        $this->order->update([
            'delivery_method' => $this->deliveryMethod,
            'delivery_address' => $this->deliveryMethod === 'delivery' ? $this->deliveryAddress : null,
            'delivery_charge' => $this->deliveryMethod === 'delivery' ? $this->deliveryCharge : 0,
        ]);

        $this->refreshOrderPricing();

        // Cash and COD have nothing to upload, so the order is complete here.
        if (! Payment::requiresProof($this->paymentMethod)) {
            $this->placeOrder();

            return;
        }

        $this->step = 3;
    }

    /**
     * Completes a cash or COD order without a screenshot. This is the moment
     * the order becomes real and appears in admin.
     */
    public function placeOrder(): void
    {
        if (Payment::requiresProof($this->paymentMethod)) {
            return;
        }

        $this->refreshOrderPricing();

        $order = $this->order->fresh();

        Payment::create([
            'order_id' => $order->id,
            'method' => $this->paymentMethod,
            'amount' => $order->grand_total,
            'proof_image' => null,
            'reference_number' => null,
            'status' => 'pending',
        ]);

        $this->order->update(['status' => Order::STATUS_PENDING]);
        $this->order->markSubmitted();

        app(OrderNotificationService::class)->notifyOrderSubmitted($this->order->fresh(['items', 'payment']));

        redirect()->route('order.show', $this->order->order_number);
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

        $this->refreshOrderPricing();

        $path = $this->proofImage->store('payment-proofs', 'public');

        Payment::create([
            'order_id' => $this->order->id,
            'method' => $this->paymentMethod,
            'amount' => $this->order->fresh()->grand_total,
            'proof_image' => $path,
            'reference_number' => $this->referenceNumber ?: null,
            'status' => 'pending',
        ]);

        $this->order->update(['status' => Order::STATUS_PENDING]);
        $this->order->markSubmitted();

        app(OrderNotificationService::class)->notifyOrderSubmitted($this->order->fresh(['items', 'payment']));

        redirect()->route('order.show', $this->order->order_number);
    }

    public function refreshOrderPricing(): void
    {
        $this->order = app(OrderPricingService::class)->refreshCartOrder($this->order);
    }

    private function loadPaymentAccounts(): void
    {
        $this->paymentAccounts = [
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
        $this->refreshOrderPricing();

        return view('livewire.checkout-page');
    }
}
