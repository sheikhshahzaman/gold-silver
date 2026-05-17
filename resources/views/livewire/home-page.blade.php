<div>

    {{-- ================================================================ --}}
    {{-- HERO SECTION — animated 3D gold bar, particles, scroll-driven      --}}
    {{-- ================================================================ --}}
    <section class="relative overflow-hidden min-h-[640px] sm:min-h-[720px]">

        {{-- Background layer (slides if any, else animated gradient) --}}
        @if($heroSlides->count())
            <div class="absolute inset-0" style="z-index: 0;"
                 x-data="{ current: 0, total: {{ $heroSlides->count() }},
                     init(){ if(this.total>1){ setInterval(()=>{this.current=(this.current+1)%this.total;},6000); } } }">
                @foreach($heroSlides as $i => $slide)
                    <div class="absolute inset-0 transition-opacity duration-[1400ms] ease-in-out"
                         :class="current === {{ $i }} ? 'opacity-100' : 'opacity-0'">
                        <img src="{{ Storage::disk('public')->url($slide->image) }}" alt="{{ $slide->title ?? 'Hero' }}"
                             class="w-full h-full object-cover scale-110"
                             data-parallax="0.15">
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Gradient veil over the background --}}
        <div class="absolute inset-0" style="z-index: 1;
             background: linear-gradient(135deg, rgba(8,28,16,0.95) 0%, rgba(10,46,35,0.82) 45%, rgba(20,16,8,0.92) 100%);"></div>

        {{-- Animated radial glows that drift with the mouse --}}
        <div class="absolute -top-32 -right-32 w-[600px] h-[600px] pointer-events-none blur-2xl opacity-50" style="z-index: 1; background: radial-gradient(circle, rgba(201,168,76,0.45) 0%, transparent 70%);"></div>
        <div class="absolute -bottom-40 -left-20 w-[520px] h-[520px] pointer-events-none blur-2xl opacity-40" style="z-index: 1; background: radial-gradient(circle, rgba(232,201,106,0.35) 0%, transparent 70%);"></div>

        {{-- Gold particle canvas --}}
        <canvas data-particles class="hero-particles"></canvas>

        {{-- Content grid --}}
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center" style="z-index: 2;">

            {{-- Left: Text + CTAs --}}
            <div class="lg:col-span-7" data-reveal-stagger="120">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] tracking-[0.2em] uppercase"
                     style="background: rgba(201,168,76,0.10); border: 1px solid rgba(201,168,76,0.35); color: #E8C96A;">
                    <span class="live-dot" style="width: 8px; height: 8px;"></span>
                    Islamabad's Most Trusted Since 2015
                </div>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl mt-6 leading-[1.05] text-white" style="font-weight: 300; letter-spacing: -0.02em;">
                    <span class="block">Pakistan's</span>
                    <span class="block shimmer-text font-medium">Premier Gold</span>
                    <span class="block">&amp; Silver Exchange</span>
                </h1>

                <p class="text-white/65 text-base sm:text-lg mt-6 leading-relaxed max-w-xl">
                    Buy and sell certified bullion at live market rates. Transparent pricing, hallmarked products, expert guidance — all in one place.
                </p>

                <div class="flex flex-wrap gap-3 mt-8">
                    <a href="/buy" data-magnetic="0.18"
                       class="group inline-flex items-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold shadow-lg shadow-gold/30 transition-shadow"
                       style="background: linear-gradient(135deg, #E8C96A 0%, #C9A84C 50%, #B8862A 100%); color: #0A2E23;">
                        Buy Gold Now
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="/live" data-magnetic="0.18"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full text-sm font-medium border transition-colors hover:bg-white/5"
                       style="color: #E8C96A; border-color: rgba(201,168,76,0.4);">
                        <span class="live-dot" style="width: 7px; height: 7px;"></span>
                        Check Live Rates
                    </a>
                </div>

                {{-- Trust metrics inline --}}
                <div class="grid grid-cols-3 gap-6 mt-12 max-w-md" data-reveal>
                    <div>
                        <div class="text-3xl text-gold" style="font-weight: 300;"
                             data-counter="10" data-counter-suffix="+">10+</div>
                        <div class="text-[11px] uppercase tracking-widest text-white/50 mt-1">Years</div>
                    </div>
                    <div>
                        <div class="text-3xl text-gold" style="font-weight: 300;"
                             data-counter="50000" data-counter-format="compact">50K+</div>
                        <div class="text-[11px] uppercase tracking-widest text-white/50 mt-1">Customers</div>
                    </div>
                    <div>
                        <div class="text-3xl text-gold" style="font-weight: 300;">
                            <span data-counter="100" data-counter-suffix="%">100%</span>
                        </div>
                        <div class="text-[11px] uppercase tracking-widest text-white/50 mt-1">Authentic</div>
                    </div>
                </div>
            </div>

            {{-- Right: 3D Gold Bar + Live Spot Card stacked --}}
            <div class="lg:col-span-5 flex flex-col items-center gap-8" data-reveal="scale">

                {{-- 3D animated gold bar --}}
                <div class="gold-bar-stage">
                    <div class="gold-bar" data-hero-bar>
                        <div class="gold-bar__face">
                            <div class="gold-bar__brand">
                                <strong>IBE</strong>
                                <span>24K · 999.9</span>
                            </div>
                        </div>
                        <div class="gold-bar__face gold-bar__face--back"></div>
                        <div class="gold-bar__face gold-bar__face--right"></div>
                        <div class="gold-bar__face gold-bar__face--left"></div>
                        <div class="gold-bar__face gold-bar__face--top"></div>
                        <div class="gold-bar__face gold-bar__face--bot"></div>
                    </div>
                </div>

                {{-- Live spot prices card --}}
                <div class="w-full max-w-sm rounded-2xl p-5 backdrop-blur-xl shadow-2xl"
                     style="background: rgba(8, 28, 16, 0.65); border: 1px solid rgba(201,168,76,0.25);">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-[11px] tracking-[0.2em] uppercase" style="color: #E8C96A;">Live Spot Prices · Per Oz</div>
                        <span class="live-dot" style="width: 8px; height: 8px;"></span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-white/[0.08]">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold" style="background: linear-gradient(135deg, #C9A84C, #A67922); color: #0A2E23;">Au</span>
                            <div>
                                <div class="text-white text-sm font-medium">Gold</div>
                                <div class="text-white/40 text-[10px] tracking-wider">XAU/USD</div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if(!empty($internationalRates['xau_usd']))
                                <div class="text-lg font-semibold" style="color: #E8C96A;" data-price="{{ $internationalRates['xau_usd'] }}" data-pkey="intl-xau-bid">${{ number_format($internationalRates['xau_usd'], 2) }}</div>
                            @else
                                <div class="text-lg" style="color: #E8C96A;">&mdash;</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-gray-400/30 flex items-center justify-center text-[11px] font-bold text-gray-200">Ag</span>
                            <div>
                                <div class="text-white text-sm font-medium">Silver</div>
                                <div class="text-white/40 text-[10px] tracking-wider">XAG/USD</div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if(!empty($internationalRates['xag_usd']))
                                <div class="text-lg font-semibold text-gray-200" data-price="{{ $internationalRates['xag_usd'] }}" data-pkey="intl-xag-bid">${{ number_format($internationalRates['xag_usd'], 2) }}</div>
                            @else
                                <div class="text-lg text-gray-200">&mdash;</div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Scroll cue --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center gap-2 opacity-70">
            <span class="text-[10px] tracking-[0.25em] uppercase" style="color: #E8C96A;">Scroll</span>
            <div class="scroll-cue"></div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- TICKER STRIP                                                      --}}
    {{-- ================================================================ --}}
    @php
        $gold24kBuy = $goldPrices['24k']['tola']['buy'] ?? null;
        $silver1kgBuy = $silverPrices['kg']['buy'] ?? null;
        $silver1kgSell = $silverPrices['kg']['sell'] ?? null;
        $gold22kBuy = $goldPrices['22k']['tola']['buy'] ?? null;
        $xauUsd = $internationalRates['xau_usd'] ?? null;
        $xagUsd = $internationalRates['xag_usd'] ?? null;
    @endphp
    <div class="overflow-hidden py-2 px-4 flex gap-8 text-xs font-medium" style="background: #C9A84C; color: #0A2E23;">
        <span class="whitespace-nowrap">Gold 24K (1 Tola): Buy Rs {{ $gold24kBuy ? number_format($gold24kBuy) : '---' }} | Sell Rs {{ isset($goldPrices['24k']['tola']['sell']) ? number_format($goldPrices['24k']['tola']['sell']) : '---' }}</span>
        <span>&bull;</span>
        <span class="whitespace-nowrap">Silver 1 KG: Buy Rs {{ $silver1kgBuy ? number_format($silver1kgBuy) : '---' }} | Sell Rs {{ $silver1kgSell ? number_format($silver1kgSell) : '---' }}</span>
        <span>&bull;</span>
        <span class="whitespace-nowrap">Gold 22K (1 Tola): Buy Rs {{ $gold22kBuy ? number_format($gold22kBuy) : '---' }}</span>
        <span>&bull;</span>
        <span class="whitespace-nowrap">XAU/USD: ${{ $xauUsd ? number_format($xauUsd, 2) : '---' }} | XAG/USD: ${{ $xagUsd ? number_format($xagUsd, 2) : '---' }}</span>
    </div>

    {{-- ================================================================ --}}
    {{-- TRUST BAR                                                         --}}
    {{-- ================================================================ --}}
    <div class="py-4 px-4 flex flex-wrap justify-center gap-6 sm:gap-10 text-xs" style="background: #F2EBD9; border-bottom: 1px solid #E0D5BC; color: #6B6B6B;">
        <span class="flex items-center gap-2"><span>🏆</span> Established 2015 &middot; 10 Years of Trust</span>
        <span class="flex items-center gap-2"><span>✅</span> 100% Authentic Hallmarked Bullion</span>
        <span class="flex items-center gap-2"><span>💰</span> Best Buy &amp; Sell Rates in Islamabad</span>
        <span class="hidden sm:flex items-center gap-2"><span>📦</span> Secure Packaging &amp; Delivery</span>
        <span class="hidden md:flex items-center gap-2"><span>📞</span> Expert Consultation Available</span>
    </div>

    {{-- Gold divider --}}
    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- ABOUT SECTION                                                     --}}
    {{-- ================================================================ --}}
    <section class="bg-white py-16 px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-10 items-center">
            {{-- Left: Stats Box --}}
            <div class="w-full lg:w-[340px] shrink-0">
                <div class="rounded-xl p-10 text-center text-white" style="background: linear-gradient(135deg, #0A2E23, #1B5E35);">
                    <div class="text-5xl font-light mb-1" style="color: #E8C96A;">2015</div>
                    <div class="text-xs tracking-widest text-white/60 mb-6">ESTABLISHED</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg p-3 text-center" style="background: rgba(255,255,255,0.08);">
                            <div class="text-xl" style="color: #E8C96A;">10+</div>
                            <div class="text-[10px] tracking-widest text-white/50">Years</div>
                        </div>
                        <div class="rounded-lg p-3 text-center" style="background: rgba(255,255,255,0.08);">
                            <div class="text-xl" style="color: #E8C96A;">50K+</div>
                            <div class="text-[10px] tracking-widest text-white/50">Customers</div>
                        </div>
                        <div class="rounded-lg p-3 text-center" style="background: rgba(255,255,255,0.08);">
                            <div class="text-xl" style="color: #E8C96A;">100%</div>
                            <div class="text-[10px] tracking-widest text-white/50">Authentic</div>
                        </div>
                        <div class="rounded-lg p-3 text-center" style="background: rgba(255,255,255,0.08);">
                            <div class="text-xl" style="color: #E8C96A;">4.8★</div>
                            <div class="text-[10px] tracking-widest text-white/50">Rating</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Text --}}
            <div>
                <div class="text-xs tracking-widest uppercase mb-2" style="color: #8B6914;">About Islamabad Bullion Exchange</div>
                <h2 class="text-3xl mb-4" style="font-weight: 400; color: #1A1A1A;">A Decade of <span style="color: #8B6914;">Craftsmanship</span><br>&amp; Trusted Legacy</h2>
                <p class="text-sm leading-relaxed mb-3" style="color: #6B6B6B;">Islamabad Bullion Exchange was established in 2015 with a vision to create timeless jewelry pieces that transcend generations. For nearly a decade, we have been the trusted name in exquisite jewelry craftsmanship, blending traditional techniques with contemporary designs.</p>
                <p class="text-sm leading-relaxed mb-5" style="color: #6B6B6B;">Our commitment to quality, authenticity, and customer satisfaction has made us a preferred choice for those who appreciate fine jewelry that tells a story and carries forward a legacy of elegance and sophistication.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach([
                        'Certified 24K, 22K & Rawa gold',
                        'Live international spot pricing',
                        'Bridal & custom jewelry orders',
                        'Zakat-compliant valuations',
                        'Secure buy-back guarantee',
                        'Expert gemologist on-site',
                    ] as $feature)
                        <div class="flex items-start gap-2 text-sm" style="color: #1A1A1A;">
                            <span class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0" style="background: #C9A84C;"></span>
                            {{ $feature }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- LIVE PRICES SECTION                                               --}}
    {{-- ================================================================ --}}
    <section class="py-14 px-4 sm:px-6 lg:px-8" style="background: #0A2E23;">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10" data-reveal>
                <div class="text-xs tracking-widest uppercase mb-2" style="color: #E8C96A;">Updated in Real-Time</div>
                <h2 class="text-2xl sm:text-3xl text-white mb-2" style="font-weight: 400;">Today's Gold &amp; Silver Rates</h2>
                <p class="text-sm text-white/50">All prices in Pakistani Rupees (PKR). International rates from XAU/XAG spot market.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" data-reveal-stagger="140">
                {{-- Gold Card --}}
                <div class="rounded-xl p-4 sm:p-5" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(201,168,76,0.2);">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold" style="background: #C9A84C; color: #0A2E23;">Au</span>
                            <div>
                                <div class="text-white text-[15px]">Gold — 24K</div>
                                <div class="text-white/40 text-[11px]">Tola / Gram rates</div>
                            </div>
                        </div>
                    </div>
                    @php
                        $goldUnits = ['tola' => '1 Tola', '10_gram' => '10 Gram', '5_gram' => '5 Gram', 'gram' => '1 Gram'];
                    @endphp
                    <table class="w-full table-fixed">
                        <thead>
                            <tr>
                                <th class="text-left text-[10px] text-white/35 font-normal pb-1"></th>
                                <th class="text-right text-[10px] text-white/35 font-normal pb-1 w-[28%]">BUY</th>
                                <th class="text-right text-[10px] text-white/35 font-normal pb-1 w-[28%]">SELL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($goldUnits as $unit => $label)
                                @php
                                    $gBuy = $goldPrices['24k'][$unit]['buy'] ?? null;
                                    $gSell = $goldPrices['24k'][$unit]['sell'] ?? null;
                                @endphp
                                <tr class="border-b border-white/[0.07] last:border-b-0">
                                    <td class="py-2 text-xs text-white/50">{{ $label }}</td>
                                    <td class="py-2 text-right text-[13px] whitespace-nowrap" style="color: #4CAF50;" @if($gBuy) data-price="{{ $gBuy }}" data-pkey="gold-24k-{{ $unit }}-buy" @endif>{{ $gBuy ? number_format($gBuy) : '---' }}</td>
                                    <td class="py-2 text-right text-[13px] whitespace-nowrap" style="color: #FF7043;" @if($gSell) data-price="{{ $gSell }}" data-pkey="gold-24k-{{ $unit }}-sell" @endif>{{ $gSell ? number_format($gSell) : '---' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Silver Card --}}
                <div class="rounded-xl p-4 sm:p-5" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(201,168,76,0.2);">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold" style="background: #9E9E9E; color: #fff;">Ag</span>
                            <div>
                                <div class="text-white text-[15px]">Silver (XAG)</div>
                                <div class="text-white/40 text-[11px]">Tola / KG rates</div>
                            </div>
                        </div>
                    </div>
                    @php
                        $silverUnits = ['kg' => '1 KG', '10_tola' => '10 Tola', 'tola' => '1 Tola', '10_gram' => '10 Gram'];
                    @endphp
                    <table class="w-full table-fixed">
                        <thead>
                            <tr>
                                <th class="text-left text-[10px] text-white/35 font-normal pb-1"></th>
                                <th class="text-right text-[10px] text-white/35 font-normal pb-1 w-[28%]">BUY</th>
                                <th class="text-right text-[10px] text-white/35 font-normal pb-1 w-[28%]">SELL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($silverUnits as $unit => $label)
                                @php
                                    $sBuy = $silverPrices[$unit]['buy'] ?? null;
                                    $sSell = $silverPrices[$unit]['sell'] ?? null;
                                @endphp
                                <tr class="border-b border-white/[0.07] last:border-b-0">
                                    <td class="py-2 text-xs text-white/50">{{ $label }}</td>
                                    <td class="py-2 text-right text-[13px] whitespace-nowrap" style="color: #4CAF50;" @if($sBuy) data-price="{{ $sBuy }}" data-pkey="silver-{{ $unit }}-buy" @endif>{{ $sBuy ? number_format($sBuy) : '---' }}</td>
                                    <td class="py-2 text-right text-[13px] whitespace-nowrap" style="color: #FF7043;" @if($sSell) data-price="{{ $sSell }}" data-pkey="silver-{{ $unit }}-sell" @endif>{{ $sSell ? number_format($sSell) : '---' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-center text-white/30 text-[11px] mt-4">Prices updated every 60 seconds from international markets. All amounts in PKR.</p>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- SHOP / PRODUCTS SECTION                                           --}}
    {{-- ================================================================ --}}
    <section class="py-14 px-4 sm:px-6 lg:px-8" style="background: #FAF6EE;" data-reveal>
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <div class="text-xs tracking-widest uppercase mb-1" style="color: #8B6914;">Buy Online</div>
                    <h2 class="text-2xl" style="font-weight: 400; color: #1A1A1A;">Shop Gold &amp; Silver Products</h2>
                </div>
                <a href="/products" class="text-sm underline" style="color: #8B6914;">View All Products →</a>
            </div>
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="/products" class="px-4 py-1.5 rounded-full text-xs cursor-pointer text-white transition-colors" style="background: #0D3D1F;">All</a>
                @foreach($productCategories as $cat)
                    <a href="/products?category={{ $cat->slug }}" class="px-4 py-1.5 rounded-full text-xs cursor-pointer text-gray-500 bg-white border border-gray-200 transition-colors hover:border-gold hover:text-gold-dark">{{ $cat->name }}</a>
                @endforeach
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" data-reveal-stagger="120">
                @foreach($products as $prod)
                    @php
                        $prodIcon = match($prod->category) {
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
                        $showPrice = $prod->productCategory?->show_live_price ?? false;
                        if (!$showPrice || $prod->price_type === 'custom_quote') {
                            $prodUrl = '/contact?subject=' . urlencode('Enquiry: ' . $prod->name);
                            $isEnquiry = true;
                        } elseif ($prod->price_key) {
                            $keyParts = explode('.', $prod->price_key);
                            $prodUrl = '/buy?metal=' . $keyParts[0] . (isset($keyParts[1]) && $keyParts[0] === 'gold' ? '&karat=' . $keyParts[1] : '') . (isset($keyParts[2]) ? '&unit=' . $keyParts[2] : (isset($keyParts[1]) && $keyParts[0] === 'silver' ? '&unit=' . $keyParts[1] : '')) . '&product=' . urlencode($prod->name);
                            $isEnquiry = false;
                        } else {
                            $prodUrl = '/buy?metal=' . $prod->metal . '&product=' . urlencode($prod->name);
                            $isEnquiry = false;
                        }
                    @endphp
                    <div class="rounded-lg overflow-hidden bg-white transition-shadow duration-300 hover:shadow-2xl" data-tilt="6" style="border: 1px solid #E8E0CC;">
                        <div class="relative">
                            @if(!$isEnquiry && $prod->discount_label)
                                <span class="absolute top-2 left-2 z-10 px-2 py-0.5 rounded text-[10px] font-bold text-white" style="background: #E53935;">{{ $prod->discount_label }}</span>
                            @endif
                            @if($prod->image)
                                <div class="h-40 flex items-center justify-center overflow-hidden" style="background: {{ $prodBg }};">
                                    <img src="{{ Storage::disk('public')->url($prod->image) }}" alt="{{ $prod->name }}" class="h-full w-full object-cover">
                                </div>
                            @else
                                <div class="h-40 flex items-center justify-center text-4xl" style="background: {{ $prodBg }};">{{ $prodIcon }}</div>
                            @endif
                        </div>
                        <div class="p-3">
                            <div class="text-sm font-medium mb-1" style="color: #1A1A1A;">{{ $prod->name }}</div>
                            <div class="text-[11px] mb-2" style="color: #6B6B6B;">{{ $prod->weight }}</div>
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($isEnquiry)
                                        <div class="text-sm font-medium" style="color: #0D3D1F;">Get Quote</div>
                                    @elseif($prod->price_type === 'fixed' && $prod->fixed_price)
                                        @if($prod->hasActiveDiscount())
                                            <div class="text-[10px] line-through" style="color: #999;">Rs {{ number_format($prod->fixed_price) }}</div>
                                            <div class="text-sm font-medium" style="color: #E53935;">Rs {{ number_format($prod->applyDiscount($prod->fixed_price)) }}</div>
                                        @else
                                            <div class="text-sm font-medium" style="color: #0D3D1F;">Rs {{ number_format($prod->fixed_price) }}</div>
                                        @endif
                                    @elseif($livePrice)
                                        @if($prod->hasActiveDiscount())
                                            <div class="text-[10px] line-through" style="color: #999;">Rs {{ number_format($livePrice) }}</div>
                                            <div class="text-sm font-medium" style="color: #E53935;">Rs {{ number_format($prod->applyDiscount($livePrice)) }}</div>
                                        @else
                                            <div class="text-sm font-medium" style="color: #0D3D1F;">Rs {{ number_format($livePrice) }}</div>
                                            <div class="text-[11px]" style="color: #6B6B6B;">Sell: Rs {{ number_format($liveSell) }}</div>
                                        @endif
                                    @else
                                        <div class="text-sm font-medium" style="color: #0D3D1F;">Contact for price</div>
                                    @endif
                                </div>
                                <a href="{{ $prodUrl }}" class="px-3 py-1.5 rounded text-[11px] text-white" style="background: #0D3D1F;">{{ $isEnquiry ? 'Enquire' : 'Buy' }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- SERVICES SECTION                                                  --}}
    {{-- ================================================================ --}}
    <section class="py-14 px-4 sm:px-6 lg:px-8 bg-white" data-reveal>
        <div class="max-w-6xl mx-auto">
            <div class="mb-10">
                <div class="text-xs tracking-widest uppercase mb-1" style="color: #8B6914;">What We Offer</div>
                <h2 class="text-2xl" style="font-weight: 400; color: #1A1A1A;">Complete Bullion &amp; Jewelry Services</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" data-reveal-stagger="120">
                @foreach($services as $svc)
                    <div class="rounded-xl p-6 transition-colors" style="border: 1px solid #E5DCC8;">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-xl mb-4" style="background: #F2EBD9;">{{ $svc->icon }}</div>
                        <h3 class="text-[15px] font-medium mb-2" style="color: #1A1A1A;">{{ $svc->title }}</h3>
                        <p class="text-[13px] leading-relaxed" style="color: #6B6B6B;">{{ $svc->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- ZAKAT CALCULATOR SECTION                                          --}}
    {{-- ================================================================ --}}
    <section class="py-14 px-4 sm:px-6 lg:px-8" style="background: #0D3D1F;">
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row items-center justify-between gap-10">
            <div class="max-w-md">
                <div class="text-xs tracking-widest uppercase mb-2" style="color: #E8C96A;">Islamic Finance Tool</div>
                <h2 class="text-2xl text-white mb-3" style="font-weight: 400;">Zakat <span style="color: #E8C96A;">Calculator</span></h2>
                <p class="text-sm leading-relaxed text-white/65 mb-3">Calculate your Zakat obligation on gold and silver holdings using live market rates. Our tool follows the Nisab threshold based on current gold and silver prices.</p>
                <p class="text-xs text-white/40">Based on current Nisab: 7.5 tola gold or 52.5 tola silver</p>
            </div>
            <div class="w-full sm:w-auto sm:min-w-[320px] rounded-xl p-6" style="background: rgba(0,0,0,0.25); border: 1px solid rgba(201,168,76,0.3);">
                <div class="mb-4">
                    <label class="block text-[11px] tracking-wider text-white/60 mb-1.5">GOLD WEIGHT (TOLAS)</label>
                    <input type="number" placeholder="e.g. 10" class="w-full px-3 py-2 rounded-md text-sm text-white" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                </div>
                <div class="mb-4">
                    <label class="block text-[11px] tracking-wider text-white/60 mb-1.5">SILVER WEIGHT (TOLAS)</label>
                    <input type="number" placeholder="e.g. 50" class="w-full px-3 py-2 rounded-md text-sm text-white" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                </div>
                <a href="/zakat-calculator" class="block w-full py-2.5 rounded-md text-sm text-center font-medium" style="background: #C9A84C; color: #0A2E23;">Calculate Zakat</a>
            </div>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- TESTIMONIALS SECTION                                              --}}
    {{-- ================================================================ --}}
    @livewire('testimonial-section')

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- STATS / WHY US SECTION                                            --}}
    {{-- ================================================================ --}}
    <section class="py-14 px-4 sm:px-6 lg:px-8" style="background: #1A1A1A;" data-reveal>
        <div class="max-w-6xl mx-auto text-center">
            <div class="text-xs tracking-widest uppercase mb-2" style="color: #E8C96A;">Our Track Record</div>
            <h2 class="text-2xl text-white mb-10" style="font-weight: 400;">Trusted by Thousands Across Pakistan</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-reveal-stagger="140">
                @foreach([
                    ['num' => $trackRecord['years'], 'label' => 'YEARS IN BUSINESS'],
                    ['num' => $trackRecord['customers'], 'label' => 'HAPPY CUSTOMERS'],
                    ['num' => $trackRecord['authentic'], 'label' => 'AUTHENTIC PRODUCTS'],
                    ['num' => $trackRecord['rating'], 'label' => 'GOOGLE RATING'],
                ] as $stat)
                    <div class="py-6">
                        <div class="text-4xl mb-1" style="color: #E8C96A; font-weight: 400;">{{ $stat['num'] }}</div>
                        <div class="text-xs tracking-wider text-white/50">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- CONTACT SECTION                                                   --}}
    {{-- ================================================================ --}}
    <section class="py-14 px-4 sm:px-6 lg:px-8" style="background: #FAF6EE;" data-reveal>
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
            {{-- Left: Info --}}
            <div>
                <div class="text-xs tracking-widest uppercase mb-1" style="color: #8B6914;">Visit or Call Us</div>
                <h2 class="text-2xl mb-4" style="font-weight: 400; color: #1A1A1A;">Get in Touch</h2>
                <p class="text-sm leading-relaxed mb-6" style="color: #6B6B6B;">We are located in the heart of Islamabad. Walk in for live rate quotes, jewelry consultations, and bullion purchases.</p>

                <div class="space-y-4">
                    @php
                        $contactItems = [
                            ['icon' => '📍', 'label' => 'Address', 'text' => $contactInfo['address']],
                            ['icon' => '📞', 'label' => 'Phone / WhatsApp', 'text' => $contactInfo['phone']],
                            ['icon' => '🕐', 'label' => 'Business Hours', 'text' => $contactInfo['hours']],
                            ['icon' => '🌐', 'label' => 'Website', 'text' => $contactInfo['website']],
                        ];
                    @endphp
                    @foreach($contactItems as $item)
                        <div class="flex items-start gap-3">
                            <span class="text-base mt-0.5">{{ $item['icon'] }}</span>
                            <div>
                                <div class="text-xs font-medium" style="color: #1A1A1A;">{{ $item['label'] }}</div>
                                <div class="text-[13px] leading-relaxed" style="color: #6B6B6B;">{{ $item['text'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Form --}}
            <div class="bg-white rounded-xl p-6" style="border: 1px solid #E5DCC8;">
                <h3 class="text-base font-medium mb-4" style="color: #1A1A1A;">Send an Enquiry</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs mb-1" style="color: #6B6B6B;">Full Name</label>
                        <input type="text" placeholder="Your name" class="w-full px-3 py-2 rounded-md text-sm" style="border: 1px solid #DDD; color: #1A1A1A;">
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color: #6B6B6B;">Phone Number</label>
                        <input type="text" placeholder="03xx xxxxxxx" class="w-full px-3 py-2 rounded-md text-sm" style="border: 1px solid #DDD; color: #1A1A1A;">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block text-xs mb-1" style="color: #6B6B6B;">Service Required</label>
                    <select class="w-full px-3 py-2 rounded-md text-sm" style="border: 1px solid #DDD; color: #1A1A1A;">
                        <option>Buy Gold</option>
                        <option>Sell Gold</option>
                        <option>Buy Silver</option>
                        <option>Sell Silver</option>
                        <option>Custom Jewelry</option>
                        <option>Zakat Valuation</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs mb-1" style="color: #6B6B6B;">Message</label>
                    <textarea rows="3" placeholder="How can we help you?" class="w-full px-3 py-2 rounded-md text-sm resize-none" style="border: 1px solid #DDD; color: #1A1A1A;"></textarea>
                </div>
                <button class="w-full py-2.5 rounded-md text-sm text-white font-medium" style="background: #0D3D1F;">Send Enquiry &rarr;</button>
            </div>
        </div>
    </section>

</div>
