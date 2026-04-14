<div>
    <section class="py-10 px-4 sm:px-6 lg:px-8" style="background: #FAF6EE; min-height: 80vh;">
        <div class="max-w-6xl mx-auto">

            <div class="mb-8">
                <div class="text-xs tracking-widest uppercase mb-1" style="color: #8B6914;">Buy Online</div>
                <h1 class="text-3xl mb-2" style="font-weight: 400; color: #1A1A1A;">Shop Gold &amp; Silver Products</h1>
                <p class="text-sm" style="color: #6B6B6B;">Browse our collection of certified gold, silver bullion and jewelry products.</p>
            </div>

            {{-- Category Tabs (dynamic from DB) --}}
            <div class="flex flex-wrap gap-2 mb-8">
                <button
                    wire:click="setCategory('all')"
                    class="px-4 py-2 rounded-full text-xs font-medium transition-all"
                    @if($categoryId === 'all')
                        style="background: #0D3D1F; color: white;"
                    @else
                        style="background: white; color: #6B6B6B; border: 1px solid #DDD;"
                    @endif
                >
                    All
                </button>
                @foreach($categories as $cat)
                    <button
                        wire:click="setCategory('{{ $cat->id }}')"
                        class="px-4 py-2 rounded-full text-xs font-medium transition-all"
                        @if($categoryId === (string) $cat->id)
                            style="background: #0D3D1F; color: white;"
                        @else
                            style="background: white; color: #6B6B6B; border: 1px solid #DDD;"
                        @endif
                    >
                        {{ $cat->icon }} {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            {{-- Products Grid --}}
            @if($products->count())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($products as $prod)
                        @php
                            $prodIcon = $prod->productCategory?->icon ?? match($prod->category) {
                                'bars' => '🥇', 'coins' => '🪙', 'silver_bars', 'silver_coins' => '🥈', 'jewelry' => '💍', default => '🥇'
                            };
                            $prodBg = $prod->metal === 'silver' ? 'linear-gradient(135deg,#E8E8E8,#C8C8C8)' : 'linear-gradient(135deg,#FBF0D0,#F5E08B)';
                            $livePrice = null; $liveSell = null;
                            if ($prod->price_type === 'live' && $prod->price_key) {
                                $parts = explode('.', $prod->price_key);
                                if (count($parts) === 3) {
                                    $livePrice = $goldPrices[$parts[1]][$parts[2]]['buy'] ?? null;
                                    $liveSell = $goldPrices[$parts[1]][$parts[2]]['sell'] ?? null;
                                } elseif (count($parts) === 2 && $parts[0] === 'silver') {
                                    $livePrice = $silverPrices[$parts[1]]['buy'] ?? null;
                                    $liveSell = $silverPrices[$parts[1]]['sell'] ?? null;
                                }
                            }
                        @endphp
                        @php
                            $showPrice = $prod->productCategory?->show_live_price ?? false;
                            if (!$showPrice || $prod->price_type === 'custom_quote') {
                                $prodUrl = '/contact?subject=' . urlencode('Enquiry: ' . $prod->name);
                                $isEnquiry = true;
                            } elseif ($prod->price_key) {
                                $keyParts = explode('.', $prod->price_key);
                                $prodUrl = '/buy?metal=' . $keyParts[0]
                                    . ($keyParts[0] === 'gold' && isset($keyParts[1]) ? '&karat=' . $keyParts[1] : '')
                                    . (isset($keyParts[2]) ? '&unit=' . $keyParts[2] : (isset($keyParts[1]) && $keyParts[0] === 'silver' ? '&unit=' . $keyParts[1] : ''))
                                    . '&product=' . urlencode($prod->name);
                                $isEnquiry = false;
                            } else {
                                $prodUrl = '/buy?metal=' . $prod->metal . '&product=' . urlencode($prod->name);
                                $isEnquiry = false;
                            }
                        @endphp
                        <div class="rounded-xl overflow-hidden bg-white shadow-sm" style="border: 1px solid #E8E0CC;">
                            <div class="relative">
                                @if($prod->discount_label)
                                    <span class="absolute top-2 left-2 z-10 px-2 py-0.5 rounded text-[10px] font-bold text-white" style="background: #E53935;">{{ $prod->discount_label }}</span>
                                @endif
                                @if($prod->image)
                                    <div class="h-48 overflow-hidden" style="background: {{ $prodBg }};">
                                        <img src="{{ Storage::disk('public')->url($prod->image) }}" alt="{{ $prod->name }}" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <div class="h-48 flex items-center justify-center text-5xl" style="background: {{ $prodBg }};">{{ $prodIcon }}</div>
                                @endif
                            </div>
                            <div class="p-4">
                                <div class="text-base font-medium mb-1" style="color: #1A1A1A;">{{ $prod->name }}</div>
                                <div class="text-xs mb-1" style="color: #6B6B6B;">{{ $prod->weight }}</div>
                                @if($prod->productCategory)
                                    <span class="inline-block text-[10px] px-2 py-0.5 rounded-full mb-2" style="background: #F2EBD9; color: #8B6914;">{{ $prod->productCategory->name }}</span>
                                @endif
                                @if($prod->description)
                                    <p class="text-xs leading-relaxed mb-3" style="color: #999;">{{ Str::limit($prod->description, 80) }}</p>
                                @endif
                                <div class="flex items-end justify-between mt-2">
                                    <div>
                                        @if($isEnquiry)
                                            <div class="text-sm font-semibold" style="color: #0D3D1F;">Get Quote</div>
                                            <div class="text-[11px]" style="color: #999;">Contact for pricing</div>
                                        @elseif($prod->price_type === 'fixed' && $prod->fixed_price)
                                            @if($prod->hasActiveDiscount())
                                                <div class="text-[11px] line-through" style="color: #999;">Rs {{ number_format($prod->fixed_price) }}</div>
                                                <div class="text-sm font-semibold" style="color: #E53935;">Rs {{ number_format($prod->applyDiscount($prod->fixed_price)) }}</div>
                                            @else
                                                <div class="text-sm font-semibold" style="color: #0D3D1F;">Rs {{ number_format($prod->fixed_price) }}</div>
                                            @endif
                                        @elseif($livePrice)
                                            @if($prod->hasActiveDiscount())
                                                <div class="text-[11px] line-through" style="color: #999;">Rs {{ number_format($livePrice) }}</div>
                                                <div class="text-sm font-semibold" style="color: #E53935;">Rs {{ number_format($prod->applyDiscount($livePrice)) }}</div>
                                            @else
                                                <div class="text-sm font-semibold" style="color: #0D3D1F;">Rs {{ number_format($livePrice) }}</div>
                                                <div class="text-[11px]" style="color: #999;">Sell: Rs {{ number_format($liveSell) }}</div>
                                            @endif
                                        @else
                                            <div class="text-sm font-semibold" style="color: #0D3D1F;">Contact for price</div>
                                        @endif
                                    </div>
                                    <a href="{{ $prodUrl }}" class="px-4 py-2 rounded text-xs text-white font-medium" style="background: #0D3D1F;">
                                        {{ $isEnquiry ? 'Enquire' : 'Buy' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <p class="text-lg" style="color: #999;">No products found in this category.</p>
                </div>
            @endif
        </div>
    </section>
</div>
