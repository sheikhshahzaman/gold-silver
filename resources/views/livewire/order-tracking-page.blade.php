<div class="min-h-screen px-4 py-10" style="background: #FAF6EE;" @if($order) wire:poll.10s="refreshOrder" @endif>
    <div class="max-w-3xl mx-auto">
        <div class="mb-8 text-center">
            <div class="section-kicker mb-2" style="color: #8B6914;">Order Status</div>
            <h1 class="font-display text-4xl sm:text-5xl font-semibold" style="color: #1A1A1A;">Track Your Order</h1>
            <p class="text-sm mt-3" style="color: #6B6B6B;">Enter your order number to see the latest status.</p>
        </div>

        <form wire:submit="track" class="glass-card p-4 sm:p-5 mb-6">
            <label class="block text-sm font-medium mb-2" style="color: #555;">Order Number</label>
            <div class="flex flex-col sm:flex-row gap-3">
                <input
                    type="text"
                    wire:model.live.debounce.200ms="orderNumber"
                    placeholder="ORD-XXXXXXXX-0000000000"
                    autocomplete="off"
                    inputmode="text"
                    oninput="
                        const body = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().replace(/^ORD/, '').slice(0, 18);
                        const first = body.slice(0, 8);
                        const second = body.slice(8, 18);
                        this.value = body ? `ORD-${first}${second ? `-${second}` : ''}` : '';
                    "
                    class="flex-1 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold/30"
                    style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;"
                >
                <button type="submit" class="btn-gold px-6 py-3 rounded-xl font-semibold">Track</button>
            </div>
            @error('orderNumber')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </form>

        @if($searched && ! $order)
            <div class="glass-card p-8 text-center">
                <p class="font-semibold" style="color: #0A2E23;">Order not found</p>
                <p class="text-sm mt-1" style="color: #6B6B6B;">Please check the order number and try again.</p>
            </div>
        @endif

        @if($order)
            <div class="glass-card p-5 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: #8B6914;">{{ $order->order_number }}</p>
                        <h2 class="text-2xl font-semibold mt-1" style="color: #0A2E23;">{{ $order->display_status }}</h2>
                    </div>
                    <span class="inline-flex self-start px-3 py-1 rounded-full text-xs font-semibold"
                        style="background: {{ $order->status === 'cancelled' ? 'rgba(239,68,68,0.12)' : 'rgba(198,150,60,0.14)' }}; color: {{ $order->status === 'cancelled' ? '#b91c1c' : '#8B6914' }};">
                        Updated {{ $order->updated_at?->format('d M Y, h:i A') }}
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($order->trackingSteps() as $step)
                        @php
                            $isDone = $step['state'] === 'complete';
                            $isCurrent = $step['state'] === 'current';
                            $isCancelled = $step['state'] === 'cancelled';
                            $isUpcoming = $step['state'] === 'upcoming';
                            $color = $isCancelled ? '#dc2626' : ($isDone ? '#16a34a' : ($isCurrent ? '#C6963C' : '#b8b1a4'));
                            $bg = $isCancelled ? 'rgba(220,38,38,0.12)' : ($isDone ? 'rgba(22,163,74,0.12)' : ($isCurrent ? 'rgba(198,150,60,0.18)' : '#EEE7DA'));
                        @endphp
                        <div class="flex items-center gap-3 {{ $isUpcoming ? 'opacity-45' : '' }}">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                                style="background: {{ $bg }}; color: {{ $color }}; border: {{ $isCurrent ? '2px solid #C6963C' : '1px solid transparent' }};">
                                @if($isCancelled)
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                @elseif($isDone)
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                @elseif($isCurrent)
                                    <span class="w-3 h-3 rounded-full" style="background: #C6963C;"></span>
                                @else
                                    <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $color }};"></span>
                                @endif
                            </div>
                            <p class="font-medium" style="color: {{ $isCurrent ? '#0A2E23' : ($isUpcoming ? '#9B9487' : '#5C5341') }};">{{ $step['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-card p-5 sm:p-6">
                <h3 class="font-semibold mb-4" style="color: #0A2E23;">Order Details</h3>
                <div class="space-y-3 text-sm">
                    @foreach($order->items as $line)
                        <div class="flex justify-between gap-4">
                            <span style="color: #5C5341;">{{ $line->quantity }} x {{ $line->product_name }}</span>
                            <span class="font-semibold" style="color: #0A2E23;">Rs {{ number_format($line->line_total, 0) }}</span>
                        </div>
                    @endforeach
                    @if($order->items->isEmpty())
                        <div class="flex justify-between gap-4">
                            <span style="color: #5C5341;">{{ $order->items_summary }}</span>
                            <span class="font-semibold" style="color: #0A2E23;">Rs {{ number_format($order->total_amount, 0) }}</span>
                        </div>
                    @endif
                    <hr style="border-color: #E8DFD0;">
                    <div class="flex justify-between gap-4">
                        <span style="color: #5C5341;">Delivery</span>
                        <span class="font-semibold text-right" style="color: #0A2E23;">{{ $order->delivery_method === 'delivery' ? 'Delivery' : 'Pickup from shop' }}</span>
                    </div>
                    @if($order->delivery_address)
                        <div class="flex justify-between gap-4">
                            <span style="color: #5C5341;">Address</span>
                            <span class="font-semibold text-right" style="color: #0A2E23;">{{ $order->delivery_address }}</span>
                        </div>
                    @endif
                    @if($order->payment)
                        <div class="flex justify-between gap-4">
                            <span style="color: #5C5341;">Payment</span>
                            <span class="font-semibold text-right" style="color: #0A2E23;">{{ ucwords(str_replace('_', ' ', $order->payment->method)) }} · {{ ucfirst($order->payment->status) }}</span>
                        </div>
                    @endif
                    <hr style="border-color: #E8DFD0;">
                    <div class="flex justify-between gap-4 text-base">
                        <span class="font-semibold" style="color: #0A2E23;">Total</span>
                        <span class="font-bold text-gold">Rs {{ number_format($order->grand_total, 0) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
