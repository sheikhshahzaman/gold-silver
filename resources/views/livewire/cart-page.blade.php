<div class="min-h-screen" style="background: #FAF6EE;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Page header --}}
        <div class="flex items-end justify-between mb-8">
            <div>
                <div class="text-xs tracking-widest uppercase mb-1" style="color: #8B6914;">Your Selection</div>
                <h1 class="text-3xl" style="font-weight: 400; color: #1A1A1A;">Shopping Cart</h1>
                <p class="text-sm mt-1" style="color: #6B6B6B;">{{ $itemCount }} {{ Str::plural('item', $itemCount) }}</p>
            </div>

            @if($itemCount > 0)
                <button wire:click="clear" onclick="return confirm('Clear all items from your cart?');"
                        class="text-xs tracking-wider uppercase transition-colors hover:underline" style="color: #8B6914;">
                    Clear cart
                </button>
            @endif
        </div>

        @if($itemCount === 0)
            {{-- Empty state --}}
            <div class="rounded-xl p-12 text-center" style="background: #FAF6EE; border: 1px solid #E5DCC8;">
                <div class="w-16 h-16 rounded-full mx-auto mb-5 flex items-center justify-center" style="background: rgba(201,168,76,0.15);">
                    <svg class="w-7 h-7" style="color: #C9A84C;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl mb-2" style="font-weight: 500; color: #1A1A1A;">Your cart is empty</h2>
                <p class="text-sm mb-6" style="color: #6B6B6B;">Browse our collection of certified gold &amp; silver products to get started.</p>
                <a href="/products" class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-medium text-white" style="background: #0D3D1F;">
                    Browse Products
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        @else
            {{-- Items + summary grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Items column --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($items as $item)
                        @php
                            $prod = $item->product;
                            $unit = (float) ($item->locked_unit_price ?? 0);
                            $bg = $prod->metal === 'silver' ? 'linear-gradient(135deg,#E8E8E8,#C8C8C8)' : 'linear-gradient(135deg,#FBF0D0,#F5E08B)';
                        @endphp
                        <div wire:key="cart-item-{{ $item->id }}"
                             class="grid grid-cols-[88px_1fr] sm:grid-cols-[112px_1fr] bg-white rounded-xl overflow-hidden"
                             style="border: 1px solid #E8E0CC;">

                            {{-- Image panel (left) --}}
                            <div class="relative" style="background: {{ $bg }};">
                                @if($prod->image)
                                    <img src="{{ Storage::disk('public')->url($prod->image) }}"
                                         alt="{{ $prod->name }}"
                                         class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center text-3xl">
                                        {{ $prod->metal === 'silver' ? '🥈' : '🥇' }}
                                    </div>
                                @endif
                            </div>

                            {{-- Right panel: title row, price row, qty row --}}
                            <div class="p-4 sm:p-5 flex flex-col min-w-0">

                                {{-- Title row + remove button --}}
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-[10px] tracking-[0.18em] uppercase mb-1"
                                             style="color: #8B6914;">
                                            {{ $prod->productCategory?->name ?? ucfirst($prod->metal) }}
                                        </div>
                                        <div class="text-[15px] sm:text-base font-medium leading-snug"
                                             style="color: #1A1A1A;">
                                            {{ $prod->name }}
                                        </div>
                                        <div class="text-xs mt-1" style="color: #6B6B6B;">
                                            {{ $prod->weight }}
                                        </div>
                                    </div>

                                    <button wire:click="remove({{ $item->id }})"
                                            title="Remove"
                                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full transition-colors text-gray-400 hover:bg-red-50 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Divider --}}
                                <div class="h-px my-3 sm:my-4" style="background: #F0E8DB;"></div>

                                {{-- Bottom row: qty stepper (left) | price (right) --}}
                                <div class="flex items-center justify-between gap-3">

                                    {{-- Qty stepper --}}
                                    <div class="inline-flex items-center bg-[#FAF6EE] rounded-full"
                                         style="border: 1px solid #E5DCC8;">
                                        <button wire:click="decrease({{ $item->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="decrease({{ $item->id }})"
                                                class="w-9 h-9 flex items-center justify-center rounded-l-full transition-colors hover:bg-white"
                                                style="color: #0D3D1F;"
                                                aria-label="Decrease quantity">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                                        </button>
                                        <span class="w-9 text-center text-sm font-semibold"
                                              style="color: #1A1A1A;">
                                            {{ $item->quantity }}
                                        </span>
                                        <button wire:click="increase({{ $item->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="increase({{ $item->id }})"
                                                class="w-9 h-9 flex items-center justify-center rounded-r-full transition-colors hover:bg-white"
                                                style="color: #0D3D1F;"
                                                aria-label="Increase quantity">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/></svg>
                                        </button>
                                    </div>

                                    {{-- Price --}}
                                    <div class="text-right">
                                        @if($unit > 0)
                                            <div class="text-base sm:text-lg font-semibold leading-none"
                                                 style="color: #0D3D1F;">
                                                Rs {{ number_format($item->line_total) }}
                                            </div>
                                            <div class="text-[11px] mt-1" style="color: #6B6B6B;">
                                                Rs {{ number_format($unit) }} × {{ $item->quantity }}
                                            </div>
                                            @if($item->packaging_charge > 0)
                                                <div class="text-[11px]" style="color: #6B6B6B;">
                                                    + Rs {{ number_format($item->packaging_charge) }} packaging
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-sm italic" style="color: #6B6B6B;">
                                                Contact for quote
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <a href="/products"
                       class="inline-flex items-center gap-2 mt-2 text-sm transition-colors hover:underline"
                       style="color: #8B6914;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                        Continue shopping
                    </a>
                </div>

                {{-- Summary column --}}
                <div class="lg:col-span-1">
                    <div class="rounded-xl p-6 sticky top-24" style="background: #FAF6EE; border: 1px solid #E5DCC8;">
                        <h3 class="text-xs tracking-widest uppercase mb-4" style="color: #8B6914;">Order Summary</h3>

                        <div class="flex justify-between py-2 text-sm" style="color: #4B4B4B;">
                            <span>Subtotal</span>
                            <span>Rs {{ number_format($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm" style="color: #4B4B4B;">
                            <span>Items</span>
                            <span>{{ $itemCount }}</span>
                        </div>

                        <div class="border-t mt-3 pt-3" style="border-color: #E5DCC8;">
                            <div class="flex justify-between items-baseline">
                                <span class="text-base font-medium" style="color: #1A1A1A;">Total</span>
                                <span class="text-2xl font-semibold" style="color: #0D3D1F;">Rs {{ number_format($subtotal) }}</span>
                            </div>
                            <p class="text-[11px] mt-1" style="color: #6B6B6B;">Tax &amp; delivery confirmed at checkout</p>
                        </div>

                        <button wire:click="checkout"
                                wire:loading.attr="disabled"
                                wire:target="checkout"
                                class="block w-full mt-5 py-3 rounded-full text-sm font-semibold text-center transition-all hover:scale-[1.02] disabled:opacity-60 disabled:cursor-wait"
                                style="background: linear-gradient(135deg, #E8C96A 0%, #C9A84C 50%, #B8862A 100%); color: #0A2E23;">
                            <span wire:loading.remove wire:target="checkout">Proceed to Checkout</span>
                            <span wire:loading wire:target="checkout">Creating order…</span>
                        </button>

                        <p class="text-[11px] text-center mt-3" style="color: #6B6B6B;">
                            Live prices refresh on this page · Final amount locked at checkout
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
