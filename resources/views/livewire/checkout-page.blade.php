<div class="max-w-2xl mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold mb-2" style="color: #0A2E23;">Checkout</h1>
        <p class="text-sm" style="color: #666;">Order #{{ $order->order_number }}</p>
    </div>

    {{-- Order Summary --}}
    <div class="glass-card p-4 mb-6">
        @php $orderItems = $order->items; @endphp

        @if($orderItems->isNotEmpty())
            {{-- Cart-based order: list each line item --}}
            <div class="text-sm font-medium mb-3" style="color: #888;">{{ ucfirst($order->type) }}ing &middot; {{ $orderItems->count() }} {{ Str::plural('item', $orderItems->count()) }}</div>
            <div class="space-y-2">
                @foreach($orderItems as $line)
                    <div class="flex items-baseline justify-between gap-3 text-sm">
                        <div class="min-w-0">
                            <p class="font-medium truncate" style="color: #0A2E23;">{{ $line->product_name }}</p>
                            <p class="text-[11px]" style="color: #888;">
                                {{ $line->quantity }} × Rs {{ number_format($line->unit_price) }}
                                @if($line->product_weight) &middot; {{ $line->product_weight }} @endif
                            </p>
                            @if($line->packaging_charge > 0)
                                <p class="text-[11px]" style="color: #888;">
                                    + Rs {{ number_format($line->packaging_charge) }} packaging × {{ $line->quantity }}
                                </p>
                            @endif
                        </div>
                        <span class="font-semibold whitespace-nowrap" style="color: #0A2E23;">Rs {{ number_format($line->line_total) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between mt-4 pt-3" style="border-top: 1px solid #E8DFD0;">
                <span class="text-sm" style="color: #888;">Total</span>
                <p class="text-xl font-bold text-gold">Rs {{ number_format($order->total_amount, 0) }}</p>
            </div>
        @else
            {{-- Legacy single-product order (Buy/Sell wizard) --}}
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium" style="color: #888;">{{ ucfirst($order->type) }}ing</span>
                    <p class="font-semibold" style="color: #0A2E23;">
                        {{ $order->quantity }} x
                        @if($order->metal === 'gold') Gold {{ strtoupper($order->karat) }} @else Silver @endif
                        ({{ str_replace('_', ' ', ucfirst($order->unit)) }})
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-sm" style="color: #888;">Total</span>
                    <p class="text-xl font-bold text-gold">Rs {{ number_format($order->total_amount, 0) }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Step Wizard Indicator --}}
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center gap-0">
            @foreach([1, 2, 3] as $s)
                <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold text-sm transition-all duration-300
                    {{ $step >= $s ? 'bg-gold text-white' : 'text-gray-400' }}" @if($step < $s) style="background: #E8DFD0;" @endif>
                    {{ $s }}
                </div>
                @if($s < 3)
                    <div class="w-16 h-0.5 transition-all duration-300
                        {{ $step > $s ? 'bg-gold' : '' }}" @if($step <= $s) style="background: #E8DFD0;" @endif></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Step 1: Customer Info --}}
    @if($step === 1)
        <div class="space-y-6">
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold mb-1" style="color: #0A2E23;">Your Details</h2>
                <p class="text-sm" style="color: #888;">Name and phone number</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #555;">Full Name</label>
                    <input type="text" wire:model="customerName" placeholder="Enter your full name"
                        class="w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold/30 transition-colors"
                        style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;">
                    @error('customerName')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #555;">Phone Number</label>
                    <input type="tel" wire:model="customerPhone" placeholder="03XX-XXXXXXX"
                        class="w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold/30 transition-colors"
                        style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;">
                    @error('customerPhone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button wire:click="goToStep2" class="btn-gold w-full text-base py-3.5">
                Continue to Payment
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </button>
        </div>

    {{-- Step 2: Delivery + Payment Method --}}
    @elseif($step === 2)
        <div class="space-y-6">
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold mb-1" style="color: #0A2E23;">Delivery &amp; Payment</h2>
                <p class="text-sm" style="color: #888;">Pickup or delivery, then how you'd like to pay</p>
            </div>

            {{-- Pickup vs Delivery --}}
            <div class="space-y-3">
                <button wire:click="selectDeliveryMethod('pickup')"
                    class="glass-card w-full p-4 text-left transition-all duration-200 cursor-pointer {{ $deliveryMethod === 'pickup' ? '!border-gold' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm" style="background: #0A2E23; color: white;">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75M13.5 6.75h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold" style="color: #0A2E23;">Pickup from our shop</p>
                            <p class="text-xs" style="color: #22c55e;">Free</p>
                        </div>
                        @if($deliveryMethod === 'pickup')
                            <svg class="w-6 h-6 text-gold" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        @endif
                    </div>
                </button>
                <button wire:click="selectDeliveryMethod('delivery')"
                    class="glass-card w-full p-4 text-left transition-all duration-200 cursor-pointer {{ $deliveryMethod === 'delivery' ? '!border-gold' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm" style="background: #0A2E23; color: white;">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25h5.379a1.125 1.125 0 01.795.33l2.246 2.245a1.125 1.125 0 01.33.795v4.253m-6.75-4.876v6.75m0 0h-3.75m3.75 0h6"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold" style="color: #0A2E23;">Delivery</p>
                            <p class="text-xs" style="color: {{ $deliveryCharge > 0 ? '#888' : '#22c55e' }};">
                                {{ $deliveryCharge > 0 ? 'Rs ' . number_format($deliveryCharge) : 'Free Delivery' }}
                            </p>
                        </div>
                        @if($deliveryMethod === 'delivery')
                            <svg class="w-6 h-6 text-gold" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        @endif
                    </div>
                </button>

                @if($deliveryMethod === 'delivery')
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: #555;">Delivery Address</label>
                        <textarea wire:model="deliveryAddress" rows="3" placeholder="House/flat, street, area, city"
                            class="w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold/30 transition-colors"
                            style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;"></textarea>
                        @error('deliveryAddress')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <div class="text-center mt-2">
                <h2 class="text-xl font-semibold mb-1" style="color: #0A2E23;">Payment Method</h2>
                <p class="text-sm" style="color: #888;">Choose how you'd like to pay</p>
            </div>

            <div class="space-y-3">
                {{-- Bank Transfer (only payment method offered) --}}
                <button wire:click="selectPaymentMethod('bank_transfer')"
                    class="glass-card w-full p-4 text-left transition-all duration-200 cursor-pointer {{ $paymentMethod === 'bank_transfer' ? '!border-gold' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm" style="background: #0A2E23; color: white;">BT</div>
                        <div class="flex-1">
                            <p class="font-semibold" style="color: #0A2E23;">Bank Transfer</p>
                            <p class="text-xs" style="color: #888;">Direct bank account transfer</p>
                        </div>
                        @if($paymentMethod === 'bank_transfer')
                            <svg class="w-6 h-6 text-gold" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        @endif
                    </div>
                </button>

                @error('paymentMethod')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Account Details --}}
            @if($paymentMethod)
                <div class="glass-card p-5">
                    @if($deliveryMethod === 'delivery' && $deliveryCharge > 0)
                        <div class="flex items-center justify-between text-sm mb-1" style="color: #888;">
                            <span>Items</span>
                            <span>Rs {{ number_format($order->total_amount, 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mb-2" style="color: #888;">
                            <span>Delivery</span>
                            <span>Rs {{ number_format($deliveryCharge, 0) }}</span>
                        </div>
                    @endif
                    <h3 class="font-semibold mb-3" style="color: #0A2E23;">
                        Send Rs {{ number_format($this->grandTotal(), 0) }} to:
                    </h3>

                    @if($paymentMethod === 'bank_transfer')
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span style="color: #888;">Bank</span><span class="font-medium" style="color: #0A2E23;">{{ $paymentAccounts['bank_transfer']['bank_name'] }}</span></div>
                            <div class="flex justify-between"><span style="color: #888;">Account Title</span><span class="font-medium" style="color: #0A2E23;">{{ $paymentAccounts['bank_transfer']['account_title'] }}</span></div>
                            <div class="flex justify-between"><span style="color: #888;">Account Number</span><span class="font-medium" style="color: #0A2E23;">{{ $paymentAccounts['bank_transfer']['account_number'] }}</span></div>
                            <div class="flex justify-between"><span style="color: #888;">IBAN</span><span class="font-medium break-all" style="color: #0A2E23;">{{ $paymentAccounts['bank_transfer']['iban'] }}</span></div>
                        </div>
                    @endif

                    <div class="mt-4 p-3 rounded-lg text-xs" style="background: rgba(198,150,60,0.1); color: #A67922; border: 1px solid rgba(198,150,60,0.2);">
                        Make payment via your own app, then come back and upload the screenshot in the next step.
                    </div>
                </div>
            @endif

            <div class="flex gap-3">
                <button wire:click="goBackToStep1"
                    class="px-6 py-3.5 rounded-lg font-semibold transition-colors" style="background: #F0E8DB; color: #0A2E23;">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </button>
                @php $canProceed = $paymentMethod && ($deliveryMethod !== 'delivery' || trim($deliveryAddress) !== ''); @endphp
                <button wire:click="goToStep3" class="btn-gold flex-1 text-base py-3.5" @if(!$canProceed) disabled style="opacity: 0.5; cursor: not-allowed;" @endif>
                    I've Sent Payment
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
            </div>
        </div>

    {{-- Step 3: Upload Proof --}}
    @else
        <div class="space-y-6">
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold mb-1" style="color: #0A2E23;">Upload Proof</h2>
                <p class="text-sm" style="color: #888;">Screenshot of your payment</p>
            </div>

            <div class="space-y-4">
                {{-- File Upload --}}
                <div wire:key="proof-upload-container">
                    <label class="block text-sm font-medium mb-1.5" style="color: #555;">Payment Screenshot</label>
                    <div class="glass-card p-6 text-center">
                        @if($proofImage)
                            <div class="mb-3">
                                <img src="{{ $proofImage->temporaryUrl() }}" alt="Payment proof" class="max-h-48 mx-auto rounded-lg">
                            </div>
                            <p class="text-xs mb-2" style="color: #22c55e;">Image selected</p>
                        @else
                            <svg class="w-12 h-12 mx-auto mb-3" style="color: #ccc;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                            </svg>
                        @endif

                        {{-- Upload loading indicator --}}
                        <div wire:loading wire:target="proofImage" class="mb-2">
                            <div class="flex items-center justify-center gap-2 text-sm" style="color: #C6963C;">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Uploading...
                            </div>
                        </div>

                        <label wire:loading.remove wire:target="proofImage" class="btn-gold text-sm py-2 px-4 cursor-pointer inline-flex items-center">
                            {{ $proofImage ? 'Change Image' : 'Choose Image' }}
                            <input type="file" wire:model="proofImage" accept="image/*" class="hidden">
                        </label>
                        <p class="text-xs mt-2" style="color: #999;">JPG, PNG up to 5MB</p>
                    </div>
                    @error('proofImage')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Reference Number --}}
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: #555;">Reference / Transaction ID (optional)</label>
                    <input type="text" wire:model="referenceNumber" placeholder="e.g. TXN123456789"
                        class="w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold/30 transition-colors"
                        style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;">
                    @error('referenceNumber')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3">
                <button wire:click="goBackToStep2"
                    class="px-6 py-3.5 rounded-lg font-semibold transition-colors" style="background: #F0E8DB; color: #0A2E23;">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </button>
                <button wire:click="submitPayment" class="btn-gold flex-1 text-base py-3.5" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitPayment">
                        Submit Payment
                        <svg class="w-5 h-5 ml-2 inline" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="submitPayment">Uploading...</span>
                </button>
            </div>
        </div>
    @endif
</div>
