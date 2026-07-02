<div class="max-w-2xl mx-auto px-4 py-6">
    {{-- Status Icon --}}
    <div class="text-center mb-6">
        @if($order->status === 'awaiting_verification')
            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: rgba(198,150,60,0.15);">
                <svg class="w-10 h-10 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2" style="color: #0A2E23;">Awaiting Verification</h1>
            <p class="text-sm" style="color: #888;">We've received your payment proof and will verify it shortly.</p>
        @elseif($order->status === 'confirmed')
            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: rgba(34,197,94,0.15);">
                <svg class="w-10 h-10" style="color: #22c55e;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2" style="color: #0A2E23;">Order Confirmed</h1>
            <p class="text-sm" style="color: #888;">Your payment has been verified. We will process your order soon.</p>
        @elseif($order->status === 'cancelled')
            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: rgba(239,68,68,0.15);">
                <svg class="w-10 h-10" style="color: #ef4444;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2" style="color: #0A2E23;">Order Cancelled</h1>
            <p class="text-sm" style="color: #888;">This order has been cancelled. Please contact us for more information.</p>
        @else
            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: rgba(59,130,246,0.15);">
                <svg class="w-10 h-10" style="color: #3b82f6;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2" style="color: #0A2E23;">{{ ucfirst($order->status) }}</h1>
            <p class="text-sm" style="color: #888;">Order #{{ $order->order_number }}</p>
        @endif
    </div>

    {{-- Order Details --}}
    <div class="glass-card p-5 mb-4">
        <h3 class="font-semibold mb-3" style="color: #0A2E23;">Order Details</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span style="color: #888;">Order Number</span>
                <span class="font-medium" style="color: #0A2E23;">{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color: #888;">Type</span>
                <span class="font-medium" style="color: #0A2E23;">{{ ucfirst($order->type) }}</span>
            </div>
            @php $orderItems = $order->items; @endphp
            @if($orderItems->isNotEmpty())
                {{-- Cart-based order: list each line item --}}
                <div class="flex flex-col gap-2 py-1">
                    <span style="color: #888;">Items ({{ $orderItems->count() }})</span>
                    @foreach($orderItems as $line)
                        <div class="flex justify-between text-sm">
                            <span style="color: #0A2E23;">
                                {{ $line->quantity }} × {{ $line->product_name }}
                                @if($line->product_weight)
                                    <span style="color: #888;">({{ $line->product_weight }})</span>
                                @endif
                                @if($line->packaging_charge > 0)
                                    <br><span class="text-[11px]" style="color: #888;">+ Rs {{ number_format($line->packaging_charge) }} packaging × {{ $line->quantity }}</span>
                                @else
                                    <br><span class="text-[11px] font-semibold" style="color: #1B8A3A;">Free packaging</span>
                                @endif
                            </span>
                            <span class="font-medium" style="color: #0A2E23;">Rs {{ number_format($line->line_total, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex justify-between">
                    <span style="color: #888;">Item</span>
                    <span class="font-medium" style="color: #0A2E23;">
                        @if($order->metal === 'gold') Gold {{ strtoupper($order->karat) }} @else Silver @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span style="color: #888;">Quantity</span>
                    <span class="font-medium" style="color: #0A2E23;">{{ $order->quantity }} {{ str_replace('_', ' ', ucfirst($order->unit)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color: #888;">Unit Price</span>
                    <span class="font-medium" style="color: #0A2E23;">Rs {{ number_format($order->locked_price, 0) }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span style="color: #888;">Delivery</span>
                <span class="font-medium" style="color: #0A2E23;">
                    {{ $order->delivery_method === 'delivery' ? 'Delivery' : 'Pickup from our shop' }}
                </span>
            </div>
            @if($order->delivery_method === 'delivery' && $order->delivery_address)
                <div class="flex justify-between gap-4">
                    <span style="color: #888;">Address</span>
                    <span class="font-medium text-right" style="color: #0A2E23;">{{ $order->delivery_address }}</span>
                </div>
            @endif
            <hr style="border-color: #E8DFD0;">
            <div class="flex justify-between">
                <span style="color: #888;">Items Total</span>
                <span class="font-medium" style="color: #0A2E23;">Rs {{ number_format($order->total_amount, 0) }}</span>
            </div>
            @if($order->delivery_charge > 0)
                <div class="flex justify-between">
                    <span style="color: #888;">Delivery Charge</span>
                    <span class="font-medium" style="color: #0A2E23;">Rs {{ number_format($order->delivery_charge, 0) }}</span>
                </div>
            @endif
            <hr style="border-color: #E8DFD0;">
            <div class="flex justify-between">
                <span class="font-semibold" style="color: #0A2E23;">Total Amount</span>
                <span class="font-bold text-gold">Rs {{ number_format($order->grand_total, 0) }}</span>
            </div>
        </div>
    </div>

    {{-- Payment Info --}}
    @if($order->payment)
        <div class="glass-card p-5 mb-4">
            <h3 class="font-semibold mb-3" style="color: #0A2E23;">Payment Info</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span style="color: #888;">Method</span>
                    <span class="font-medium" style="color: #0A2E23;">{{ ucfirst(str_replace('_', ' ', $order->payment->method)) }}</span>
                </div>
                @if($order->payment->reference_number)
                    <div class="flex justify-between">
                        <span style="color: #888;">Reference</span>
                        <span class="font-medium" style="color: #0A2E23;">{{ $order->payment->reference_number }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span style="color: #888;">Status</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                        @if($order->payment->status === 'pending') text-yellow-700 @elseif($order->payment->status === 'verified') text-green-700 @else text-red-700 @endif"
                        style="background: @if($order->payment->status === 'pending') rgba(234,179,8,0.15) @elseif($order->payment->status === 'verified') rgba(34,197,94,0.15) @else rgba(239,68,68,0.15) @endif;">
                        {{ ucfirst($order->payment->status) }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- Customer Info --}}
    @if($order->customer_name)
        <div class="glass-card p-5 mb-4">
            <h3 class="font-semibold mb-3" style="color: #0A2E23;">Customer Info</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span style="color: #888;">Name</span>
                    <span class="font-medium" style="color: #0A2E23;">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color: #888;">Phone</span>
                    <span class="font-medium" style="color: #0A2E23;">{{ $order->customer_phone }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Contact Us --}}
    @if($whatsappNumber || $contactPhone || $contactAddress)
        <div class="glass-card p-5 mb-4">
            <h3 class="font-semibold mb-3" style="color: #0A2E23;">Contact Us</h3>
            <p class="text-xs mb-3" style="color: #888;">Questions about your order? Reach us here.</p>

            @if($whatsappNumber)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}?text={{ urlencode('Hi, I placed an order #' . $order->order_number . '. Please confirm.') }}"
                    target="_blank" rel="noopener"
                    class="flex items-center gap-3 mb-3 transition-opacity duration-200 hover:opacity-80">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: #25D366;">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: #0A2E23;">WhatsApp</p>
                        <p class="text-xs" style="color: #888;">{{ $whatsappNumber }}</p>
                    </div>
                </a>
            @endif

            @if($contactPhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="flex items-center gap-3 mb-3 transition-opacity duration-200 hover:opacity-80">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: #0A2E23;">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: #0A2E23;">Call Us</p>
                        <p class="text-xs" style="color: #888;">{{ $contactPhone }}</p>
                    </div>
                </a>
            @endif

            @if($contactAddress)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: #0A2E23;">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: #0A2E23;">Visit Us</p>
                        <p class="text-xs" style="color: #888;">{{ $contactAddress }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Back to Home --}}
    <a href="{{ route('home') }}" class="btn-gold w-full text-base py-3.5 block text-center">
        Back to Home
    </a>
</div>
