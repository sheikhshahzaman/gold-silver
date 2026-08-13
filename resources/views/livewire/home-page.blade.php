<div>

    {{-- ================================================================ --}}
    {{-- HERO SECTION with Image Slider                                    --}}
    {{-- ================================================================ --}}
    <section class="relative overflow-hidden min-h-[500px] sm:min-h-[560px]"
        x-data="{
            current: 0,
            total: {{ $heroSlides->count() ?: 0 }},
            init() {
                if (this.total > 1) {
                    setInterval(() => { this.current = (this.current + 1) % this.total; }, 6000);
                }
            }
        }">

        {{-- Background: slides or fallback gradient --}}
        @if($heroSlides->count())
            @foreach($heroSlides as $i => $slide)
                <div class="absolute inset-0 transition-opacity duration-1000 overflow-hidden"
                     :class="current === {{ $i }} ? 'opacity-100 hero-slide-active' : 'opacity-0'"
                     style="z-index: 0;">
                    <img src="{{ Storage::disk('public')->url($slide->image) }}" alt="{{ $slide->title ?? 'Hero' }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(10,31,16,0.88) 0%, rgba(13,61,31,0.72) 50%, rgba(26,26,10,0.82) 100%);"></div>
                </div>
            @endforeach
        @else
            <div class="absolute inset-0" style="background: linear-gradient(135deg, #0A1F10 0%, #0D3D1F 50%, #1A1A0A 100%); z-index: 0;"></div>
        @endif

        {{-- Radial glows + particles --}}
        <div class="absolute -top-1/2 -right-[10%] w-[560px] h-[560px] pointer-events-none" style="background: radial-gradient(circle, rgba(201,168,76,0.1) 0%, transparent 70%); z-index: 1;"></div>
        <div class="absolute -bottom-1/3 -left-[10%] w-[420px] h-[420px] pointer-events-none" style="background: radial-gradient(circle, rgba(201,168,76,0.06) 0%, transparent 70%); z-index: 1;"></div>

        {{-- Content --}}
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 flex flex-col lg:flex-row items-center justify-between gap-10" style="z-index: 2;">
            {{-- Left: Text --}}
            <div class="max-w-xl">
                <div data-reveal class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[11px] tracking-[0.18em] uppercase mb-6" style="background: rgba(201,168,76,0.12); border: 1px solid rgba(201,168,76,0.4); color: #E8C96A; backdrop-filter: blur(4px);">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    Islamabad's Most Trusted Since 2015
                </div>
                <h1 data-reveal data-reveal-delay="100" class="font-display font-semibold text-5xl sm:text-6xl text-white leading-[1.05] mb-5">
                    Pakistan's Premier<br>
                    <span class="italic" style="background: linear-gradient(110deg, #C9A84C 20%, #F4E2A0 50%, #C9A84C 80%); -webkit-background-clip: text; background-clip: text; color: transparent;">Gold &amp; Silver</span><br>
                    Exchange
                </h1>
                <p data-reveal data-reveal-delay="200" class="text-white/65 text-base leading-relaxed mb-8 max-w-md">
                    Buy and sell certified gold and silver at live market rates. Transparent pricing, authentic hallmarked products, and expert guidance — all in one place.
                </p>
                <div data-reveal data-reveal-delay="300" class="flex flex-wrap gap-3">
                    <a href="/products" class="group inline-flex items-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold transition-all duration-300 hover:shadow-[0_10px_30px_rgba(201,168,76,0.45)] hover:-translate-y-0.5" style="background: linear-gradient(135deg, #E8C96A, #C9A84C 60%, #A67922); color: #0A2E23;">
                        Shop Products
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                    </a>
                    <a href="/live" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full text-sm font-medium border transition-all duration-300 hover:bg-white/10 hover:-translate-y-0.5" style="color: #E8C96A; border-color: rgba(201,168,76,0.5); backdrop-filter: blur(4px);">
                        <span class="live-dot"></span>
                        Check Live Rates
                    </a>
                </div>

                {{-- Slide dots --}}
                @if($heroSlides->count() > 1)
                    <div class="flex gap-2 mt-10">
                        @foreach($heroSlides as $i => $slide)
                            <button @click="current = {{ $i }}" aria-label="Slide {{ $i + 1 }}" class="h-2.5 rounded-full transition-all duration-500 cursor-pointer" :class="current === {{ $i }} ? 'w-8' : 'w-2.5'" :style="current === {{ $i }} ? 'background: #C9A84C;' : 'background: rgba(255,255,255,0.3);'"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Live Ticker Card --}}
            <div data-reveal="right" data-reveal-delay="250" class="w-full sm:w-auto sm:min-w-[320px] rounded-2xl p-[1.5px]" style="background: linear-gradient(160deg, rgba(232,201,106,0.55), rgba(201,168,76,0.1) 45%, rgba(232,201,106,0.35));">
                <div class="rounded-[15px] p-5 backdrop-blur-md" style="background: rgba(5,18,12,0.78);">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[11px] tracking-[0.18em] font-medium" style="color: #E8C96A;">LIVE SPOT PRICES <span class="text-white/30 normal-case tracking-normal">(per oz)</span></span>
                        <span class="live-dot"></span>
                    </div>

                    {{-- Gold row --}}
                    <div class="flex items-center justify-between py-3.5 border-b border-white/10">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold shadow-[0_0_14px_rgba(201,168,76,0.4)]" style="background: linear-gradient(135deg, #E8C96A, #A67922); color: #0A2E23;">Au</span>
                            <div>
                                <div class="text-white text-sm font-medium">Gold</div>
                                <div class="text-white/40 text-[10px] tracking-wide">XAU/USD</div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if(!empty($internationalRates['xau_usd']))
                                <div class="text-lg font-semibold tabular-nums" style="color: #E8C96A;" data-price="{{ $internationalRates['xau_usd'] }}" data-pkey="intl-xau-bid">${{ number_format($internationalRates['xau_usd'], 2) }}</div>
                            @else
                                <div class="text-lg" style="color: #E8C96A;">&mdash;</div>
                            @endif
                        </div>
                    </div>

                    {{-- Silver row --}}
                    <div class="flex items-center justify-between py-3.5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold shadow-[0_0_14px_rgba(158,158,158,0.35)]" style="background: linear-gradient(135deg, #E0E0E0, #8C8C8C); color: #1A1A1A;">Ag</span>
                            <div>
                                <div class="text-white text-sm font-medium">Silver</div>
                                <div class="text-white/40 text-[10px] tracking-wide">XAG/USD</div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if(!empty($internationalRates['xag_usd']))
                                <div class="text-lg font-semibold tabular-nums text-gray-200" data-price="{{ $internationalRates['xag_usd'] }}" data-pkey="intl-xag-bid">${{ number_format($internationalRates['xag_usd'], 2) }}</div>
                            @else
                                <div class="text-lg text-gray-300">&mdash;</div>
                            @endif
                        </div>
                    </div>

                    <a href="/live" class="mt-3 flex items-center justify-center gap-1.5 w-full py-2 rounded-lg text-[11px] font-medium transition-colors hover:bg-white/5" style="color: rgba(232,201,106,0.8); border: 1px solid rgba(201,168,76,0.25);">
                        View all rates
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- TICKER STRIP (scrolling marquee)                                  --}}
    {{-- ================================================================ --}}
    @php
        $gold24kBuy = $goldPrices['24k']['tola']['buy'] ?? null;
        $gold24kSell = $goldPrices['24k']['tola']['sell'] ?? null;
        $silver1kgBuy = $silverPrices['kg']['buy'] ?? null;
        $silver1kgSell = $silverPrices['kg']['sell'] ?? null;
        $gold22kBuy = $goldPrices['22k']['tola']['buy'] ?? null;
        $xauUsd = $internationalRates['xau_usd'] ?? null;
        $xagUsd = $internationalRates['xag_usd'] ?? null;
        $tickerItems = [
            'Gold 24K (1 Tola): Buy Rs '.($gold24kBuy ? number_format($gold24kBuy) : '---').' | Sell Rs '.($gold24kSell ? number_format($gold24kSell) : '---'),
            'Silver 1 KG: Buy Rs '.($silver1kgBuy ? number_format($silver1kgBuy) : '---').' | Sell Rs '.($silver1kgSell ? number_format($silver1kgSell) : '---'),
            'Gold 22K (1 Tola): Buy Rs '.($gold22kBuy ? number_format($gold22kBuy) : '---'),
            'XAU/USD: $'.($xauUsd ? number_format($xauUsd, 2) : '---').' | XAG/USD: $'.($xagUsd ? number_format($xagUsd, 2) : '---'),
        ];
    @endphp
    <div class="ticker-wrapper py-2.5 text-xs font-semibold" style="background: linear-gradient(90deg, #A67922, #C9A84C 30%, #E8C96A 50%, #C9A84C 70%, #A67922); color: #0A2E23;">
        <div class="ticker-content">
            @for($pass = 0; $pass < 2; $pass++)
                @foreach($tickerItems as $tick)
                    <span class="inline-flex items-center gap-2 px-6">
                        <svg class="w-3 h-3 opacity-70" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                        {{ $tick }}
                    </span>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- TRUST BAR                                                         --}}
    {{-- ================================================================ --}}
    @php
        $trustItems = [
            ['icon' => 'M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0', 'text' => 'Established 2015 · 10 Years of Trust', 'always' => true],
            ['icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', 'text' => '100% Authentic Hallmarked Bullion', 'always' => true],
            ['icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z', 'text' => 'Best Buy & Sell Rates in Islamabad', 'always' => true],
            ['icon' => 'm21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9', 'text' => 'Secure Packaging & Delivery', 'class' => 'hidden sm:flex'],
            ['icon' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z', 'text' => 'Expert Consultation Available', 'class' => 'hidden md:flex'],
        ];
    @endphp
    <div class="py-4 px-4 flex flex-wrap justify-center gap-x-8 gap-y-2.5 text-xs" style="background: #F2EBD9; border-bottom: 1px solid #E0D5BC; color: #5C5341;">
        @foreach($trustItems as $i => $trust)
            <span data-reveal data-reveal-delay="{{ $i * 80 }}" class="{{ $trust['class'] ?? 'flex' }} items-center gap-2">
                <svg class="w-4 h-4 shrink-0" style="color: #A67922;" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $trust['icon'] }}"/></svg>
                {{ $trust['text'] }}
            </span>
        @endforeach
    </div>

    {{-- Gold divider --}}
    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- ABOUT SECTION                                                     --}}
    {{-- ================================================================ --}}
    <section class="bg-white py-16 sm:py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-12 items-center">
            {{-- Left: Stats Box --}}
            <div data-reveal="left" class="w-full lg:w-[360px] shrink-0">
                <div class="relative rounded-2xl p-[1.5px] shadow-[0_20px_50px_rgba(10,46,35,0.25)]" style="background: linear-gradient(150deg, rgba(232,201,106,0.8), rgba(201,168,76,0.2) 45%, rgba(232,201,106,0.6));">
                    <div class="rounded-[15px] p-10 text-center text-white" style="background: linear-gradient(135deg, #0A2E23, #1B5E35);">
                        <div class="font-display text-6xl font-semibold mb-1" style="color: #E8C96A;" data-countup>2015</div>
                        <div class="text-[11px] tracking-[0.25em] text-white/60 mb-7">ESTABLISHED</div>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([['10+', 'Years'], ['50K+', 'Customers'], ['100%', 'Authentic'], ['4.8★', 'Rating']] as $stat)
                                <div class="rounded-xl p-3.5 text-center transition-colors duration-300 hover:bg-white/[0.14]" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(232,201,106,0.12);">
                                    <div class="text-xl font-semibold tabular-nums" style="color: #E8C96A;" data-countup>{{ $stat[0] }}</div>
                                    <div class="text-[10px] tracking-[0.18em] text-white/50 uppercase mt-0.5">{{ $stat[1] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Text --}}
            <div data-reveal="right" data-reveal-delay="120">
                <div class="section-kicker mb-3" style="color: #8B6914;">About Islamabad Bullion Exchange</div>
                <h2 class="font-display font-semibold text-4xl sm:text-[2.75rem] leading-tight mb-5" style="color: #1A1A1A;">A Decade of <span class="italic" style="color: #8B6914;">Craftsmanship</span><br>&amp; Trusted Legacy</h2>
                <p class="text-sm leading-relaxed mb-3" style="color: #6B6B6B;">Islamabad Bullion Exchange was established in 2015 with a vision to create timeless jewelry pieces that transcend generations. For nearly a decade, we have been the trusted name in exquisite jewelry craftsmanship, blending traditional techniques with contemporary designs.</p>
                <p class="text-sm leading-relaxed mb-6" style="color: #6B6B6B;">Our commitment to quality, authenticity, and customer satisfaction has made us a preferred choice for those who appreciate fine jewelry that tells a story and carries forward a legacy of elegance and sophistication.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach([
                        'Certified 24K, 22K & Rawa gold',
                        'Live international spot pricing',
                        'Bridal & custom jewelry orders',
                        'Zakat-compliant valuations',
                        'Secure buy-back guarantee',
                        'Expert gemologist on-site',
                    ] as $i => $feature)
                        <div data-reveal data-reveal-delay="{{ 150 + $i * 60 }}" class="flex items-center gap-2.5 text-sm" style="color: #1A1A1A;">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style="background: rgba(201,168,76,0.15);">
                                <svg class="w-3 h-3" style="color: #A67922;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </span>
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
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden" style="background: radial-gradient(ellipse at top, #0D3D2A 0%, #0A2E23 60%);">
        <div class="absolute top-0 right-0 w-[400px] h-[400px] pointer-events-none" style="background: radial-gradient(circle, rgba(201,168,76,0.07) 0%, transparent 70%);"></div>
        <div class="max-w-6xl mx-auto relative">
            <div class="text-center mb-12" data-reveal>
                <div class="inline-flex items-center gap-2 mb-3 text-[11px] tracking-[0.22em] uppercase font-semibold" style="color: #E8C96A;">
                    <span class="live-dot"></span>
                    Updated in Real-Time
                </div>
                <h2 class="font-display font-semibold text-4xl sm:text-[2.75rem] text-white mb-3">Today's Gold &amp; Silver Rates</h2>
                <p class="text-sm text-white/50 max-w-lg mx-auto">All prices in Pakistani Rupees (PKR). International rates from XAU/XAG spot market.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- Gold Card --}}
                <div data-reveal data-reveal-delay="100" class="rounded-2xl p-[1.5px] transition-transform duration-300 hover:-translate-y-1" style="background: linear-gradient(150deg, rgba(232,201,106,0.5), rgba(201,168,76,0.08) 50%, rgba(232,201,106,0.3));">
                    <div class="rounded-[15px] p-5 sm:p-6 h-full" style="background: rgba(6,22,16,0.75); backdrop-filter: blur(8px);">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold shadow-[0_0_18px_rgba(201,168,76,0.45)]" style="background: linear-gradient(135deg, #E8C96A, #A67922); color: #0A2E23;">Au</span>
                                <div>
                                    <div class="text-white text-base font-medium">Gold — 24K</div>
                                    <div class="text-white/40 text-[11px]">Tola / Gram rates</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] tracking-wider font-semibold" style="background: rgba(201,168,76,0.12); border: 1px solid rgba(201,168,76,0.3); color: #E8C96A;">PKR</span>
                        </div>
                        @php
                            $goldUnits = ['tola' => '1 Tola', '10_gram' => '10 Gram', '5_gram' => '5 Gram', 'gram' => '1 Gram'];
                        @endphp
                        <table class="w-full table-fixed">
                            <thead>
                                <tr>
                                    <th class="text-left text-[10px] text-white/35 font-normal pb-2 tracking-wider"></th>
                                    <th class="text-right text-[10px] text-white/35 font-normal pb-2 w-[28%] tracking-wider">BUY</th>
                                    <th class="text-right text-[10px] text-white/35 font-normal pb-2 w-[28%] tracking-wider">SELL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($goldUnits as $unit => $label)
                                    @php
                                        $gBuy = $goldPrices['24k'][$unit]['buy'] ?? null;
                                        $gSell = $goldPrices['24k'][$unit]['sell'] ?? null;
                                    @endphp
                                    <tr class="border-b border-white/[0.07] last:border-b-0 transition-colors hover:bg-white/[0.03]">
                                        <td class="py-2.5 text-xs text-white/55">{{ $label }}</td>
                                        <td class="py-2.5 text-right text-[13px] whitespace-nowrap tabular-nums" style="color: #4CAF50;" @if($gBuy) data-price="{{ $gBuy }}" data-pkey="gold-24k-{{ $unit }}-buy" @endif>{{ $gBuy ? number_format($gBuy) : '---' }}</td>
                                        <td class="py-2.5 text-right text-[13px] whitespace-nowrap tabular-nums" style="color: #FF7043;" @if($gSell) data-price="{{ $gSell }}" data-pkey="gold-24k-{{ $unit }}-sell" @endif>{{ $gSell ? number_format($gSell) : '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Silver Card --}}
                <div data-reveal data-reveal-delay="200" class="rounded-2xl p-[1.5px] transition-transform duration-300 hover:-translate-y-1" style="background: linear-gradient(150deg, rgba(224,224,224,0.4), rgba(158,158,158,0.08) 50%, rgba(224,224,224,0.25));">
                    <div class="rounded-[15px] p-5 sm:p-6 h-full" style="background: rgba(6,22,16,0.75); backdrop-filter: blur(8px);">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold shadow-[0_0_18px_rgba(158,158,158,0.35)]" style="background: linear-gradient(135deg, #E0E0E0, #8C8C8C); color: #1A1A1A;">Ag</span>
                                <div>
                                    <div class="text-white text-base font-medium">Silver (XAG)</div>
                                    <div class="text-white/40 text-[11px]">Tola / KG rates</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] tracking-wider font-semibold" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #D6D6D6;">PKR</span>
                        </div>
                        @php
                            $silverUnits = [
                                '10_tola_qr' => ['title' => '10 Tola', 'subtitle' => '(QR Packaging)'],
                                '10_tola' => ['title' => '10 Tola', 'subtitle' => '(999)'],
                                'kg' => ['title' => '1 KG', 'subtitle' => null],
                                '5_tola' => ['title' => '5 Tola', 'subtitle' => '(Bar)'],
                                'tola' => ['title' => '1 Tola', 'subtitle' => '(Bar)'],
                            ];
                        @endphp
                        <table class="w-full table-fixed">
                            <thead>
                                <tr>
                                    <th class="text-left text-[10px] text-white/35 font-normal pb-2 tracking-wider"></th>
                                    <th class="text-right text-[10px] text-white/35 font-normal pb-2 w-[28%] tracking-wider">BUY</th>
                                    <th class="text-right text-[10px] text-white/35 font-normal pb-2 w-[28%] tracking-wider">SELL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($silverUnits as $unit => $label)
                                    @php
                                        $sBuy = $silverPrices[$unit]['buy'] ?? null;
                                        $sSell = $silverPrices[$unit]['sell'] ?? null;
                                    @endphp
                                    <tr class="border-b border-white/[0.07] last:border-b-0 transition-colors hover:bg-white/[0.03]">
                                        <td class="py-2.5 text-xs text-white/55">
                                            <span class="block">{{ $label['title'] }}</span>
                                            @if($label['subtitle'])
                                                <span class="block text-[10px] text-white/35">{{ $label['subtitle'] }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 text-right text-[13px] whitespace-nowrap tabular-nums" style="color: #4CAF50;" @if($sBuy) data-price="{{ $sBuy }}" data-pkey="silver-{{ $unit }}-buy" @endif>{{ $sBuy ? number_format($sBuy) : '---' }}</td>
                                        <td class="py-2.5 text-right text-[13px] whitespace-nowrap tabular-nums" style="color: #FF7043;" @if($sSell) data-price="{{ $sSell }}" data-pkey="silver-{{ $unit }}-sell" @endif>{{ $sSell ? number_format($sSell) : '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <p class="text-center text-white/30 text-[11px] mt-5">Prices updated every 60 seconds from international markets. All amounts in PKR.</p>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- SHOP / PRODUCTS SECTION                                           --}}
    {{-- ================================================================ --}}
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(180deg, #FAF6EE 0%, #F4EDDD 100%);">
        <div class="max-w-6xl mx-auto">
            <div data-reveal class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <div class="section-kicker mb-2" style="color: #8B6914;">Buy Online</div>
                    <h2 class="font-display font-semibold text-4xl sm:text-[2.75rem] leading-tight" style="color: #1A1A1A;">Shop Gold &amp; Silver <span class="italic" style="color: #8B6914;">Products</span></h2>
                </div>
                <a href="/products" class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all duration-300 hover:shadow-[0_8px_22px_rgba(13,61,31,0.3)] hover:-translate-y-0.5" style="background: #0D3D1F; color: #F4E2A0;">
                    View All Products
                    <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                </a>
            </div>

            {{-- Category pills --}}
            <div data-reveal data-reveal-delay="100" class="flex gap-2 mb-8 overflow-x-auto no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap">
                <a href="/products" class="shrink-0 px-5 py-2 rounded-full text-xs font-semibold cursor-pointer transition-all duration-300 shadow-[0_4px_12px_rgba(13,61,31,0.25)]" style="background: linear-gradient(135deg, #0D3D1F, #165A42); color: #F4E2A0;">All</a>
                @foreach($productCategories as $cat)
                    <a href="/products?category={{ $cat->slug }}" class="shrink-0 px-5 py-2 rounded-full text-xs font-medium cursor-pointer text-gray-600 bg-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(139,105,20,0.18)]" style="border: 1px solid #E3D9C2;">{{ $cat->name }}</a>
                @endforeach
            </div>

            @error('cart')
                <div class="mb-5 rounded-xl px-4 py-3 text-sm" style="background: #FFF3CD; color: #7A4F00; border: 1px solid #F5D77A;">
                    {{ $message }}
                </div>
            @enderror

            {{-- Product grid --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($products as $idx => $prod)
                    <x-product-card
                        :prod="$prod"
                        :gold-prices="$goldPrices"
                        :silver-prices="$silverPrices"
                        :added="!empty($justAdded[$prod->id])"
                        :index="$idx"
                    />
                @endforeach
            </div>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- SERVICES SECTION                                                  --}}
    {{-- ================================================================ --}}
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-6xl mx-auto">
            <div data-reveal class="mb-12 text-center sm:text-left">
                <div class="section-kicker mb-2 justify-center sm:justify-start" style="color: #8B6914;">What We Offer</div>
                <h2 class="font-display font-semibold text-4xl sm:text-[2.75rem] leading-tight" style="color: #1A1A1A;">Complete Bullion &amp; <span class="italic" style="color: #8B6914;">Jewelry</span> Services</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($services as $i => $svc)
                    <div data-reveal data-reveal-delay="{{ ($i % 3) * 100 }}" class="group rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_18px_40px_rgba(139,105,20,0.14)]" style="border: 1px solid #E5DCC8; background: linear-gradient(180deg, #FFFFFF, #FDFAF3);">
                        <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center text-2xl mb-5 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3" style="background: linear-gradient(135deg, #F7EFDB, #EFE2BE); border: 1px solid rgba(201,168,76,0.25);">{{ $svc->icon }}</div>
                        <h3 class="text-base font-semibold mb-2 transition-colors duration-300 group-hover:text-[#8B6914]" style="color: #1A1A1A;">{{ $svc->title }}</h3>
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
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden" style="background: radial-gradient(ellipse at bottom left, #14532D 0%, #0D3D1F 55%);">
        <div class="absolute top-0 right-0 w-[460px] h-[460px] pointer-events-none" style="background: radial-gradient(circle, rgba(201,168,76,0.08) 0%, transparent 70%);"></div>
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row items-center justify-between gap-12 relative">
            <div data-reveal="left" class="max-w-md text-center lg:text-left">
                <div class="section-kicker mb-3 justify-center lg:justify-start" style="color: #E8C96A;">Islamic Finance Tool</div>
                <h2 class="font-display font-semibold text-4xl sm:text-[2.75rem] text-white mb-4">Zakat <span class="italic" style="color: #E8C96A;">Calculator</span></h2>
                <p class="text-sm leading-relaxed text-white/65 mb-4">Calculate your Zakat obligation on gold and silver holdings using live market rates. Our tool follows the Nisab threshold based on current gold and silver prices.</p>
                <p class="inline-flex items-center gap-2 text-xs text-white/40">
                    <svg class="w-3.5 h-3.5 shrink-0" style="color: rgba(232,201,106,0.6);" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    Based on current Nisab: 7.5 tola gold or 52.5 tola silver
                </p>
            </div>
            <div data-reveal="right" data-reveal-delay="120" class="w-full sm:w-auto sm:min-w-[340px] rounded-2xl p-[1.5px]" style="background: linear-gradient(150deg, rgba(232,201,106,0.6), rgba(201,168,76,0.12) 50%, rgba(232,201,106,0.4));">
                <div class="rounded-[15px] p-6 sm:p-7" style="background: rgba(4,16,10,0.55); backdrop-filter: blur(8px);">
                    <div class="mb-4">
                        <label class="block text-[11px] tracking-[0.18em] text-white/60 mb-2">GOLD WEIGHT (TOLAS)</label>
                        <input type="number" placeholder="e.g. 10" class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder:text-white/30 transition-all focus:outline-none" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                               onfocus="this.style.borderColor='#E8C96A'; this.style.boxShadow='0 0 0 3px rgba(232,201,106,0.12)'"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                    </div>
                    <div class="mb-5">
                        <label class="block text-[11px] tracking-[0.18em] text-white/60 mb-2">SILVER WEIGHT (TOLAS)</label>
                        <input type="number" placeholder="e.g. 50" class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder:text-white/30 transition-all focus:outline-none" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
                               onfocus="this.style.borderColor='#E8C96A'; this.style.boxShadow='0 0 0 3px rgba(232,201,106,0.12)'"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                    </div>
                    <a href="/zakat-calculator" class="group flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm text-center font-semibold transition-all duration-300 hover:shadow-[0_10px_26px_rgba(201,168,76,0.4)] hover:-translate-y-0.5" style="background: linear-gradient(135deg, #E8C96A, #C9A84C 60%, #A67922); color: #0A2E23;">
                        Calculate Zakat
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                    </a>
                </div>
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
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden" style="background: radial-gradient(ellipse at top, #232323 0%, #161616 65%);">
        <div class="absolute inset-x-0 top-0 h-px" style="background: linear-gradient(90deg, transparent, rgba(232,201,106,0.4), transparent);"></div>
        <div class="max-w-6xl mx-auto text-center relative">
            <div data-reveal class="section-kicker mb-3 justify-center" style="color: #E8C96A;">Our Track Record</div>
            <h2 data-reveal data-reveal-delay="80" class="font-display font-semibold text-4xl sm:text-[2.75rem] text-white mb-12">Trusted by Thousands Across Pakistan</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([
                    ['num' => $trackRecord['years'], 'label' => 'YEARS IN BUSINESS'],
                    ['num' => $trackRecord['customers'], 'label' => 'HAPPY CUSTOMERS'],
                    ['num' => $trackRecord['authentic'], 'label' => 'AUTHENTIC PRODUCTS'],
                    ['num' => $trackRecord['rating'], 'label' => 'GOOGLE RATING'],
                ] as $i => $stat)
                    <div data-reveal data-reveal-delay="{{ $i * 100 }}" class="py-8 rounded-2xl transition-colors duration-300 hover:bg-white/[0.04]" style="border: 1px solid rgba(232,201,106,0.1);">
                        <div class="font-display text-5xl font-semibold mb-2 tabular-nums" style="background: linear-gradient(160deg, #F4E2A0, #C9A84C); -webkit-background-clip: text; background-clip: text; color: transparent;" data-countup>{{ $stat['num'] }}</div>
                        <div class="text-[10px] tracking-[0.2em] text-white/45">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="h-[3px]" style="background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>

    {{-- ================================================================ --}}
    {{-- CONTACT SECTION                                                   --}}
    {{-- ================================================================ --}}
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(180deg, #FAF6EE 0%, #F4EDDD 100%);">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            {{-- Left: Info --}}
            <div data-reveal="left">
                <div class="section-kicker mb-2" style="color: #8B6914;">Visit or Call Us</div>
                <h2 class="font-display font-semibold text-4xl sm:text-[2.75rem] leading-tight mb-5" style="color: #1A1A1A;">Get in <span class="italic" style="color: #8B6914;">Touch</span></h2>
                <p class="text-sm leading-relaxed mb-8" style="color: #6B6B6B;">We are located in the heart of Islamabad. Walk in for live rate quotes, jewelry consultations, and bullion purchases.</p>

                <div class="space-y-3">
                    @php
                        $contactItems = [
                            ['icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z', 'label' => 'Address', 'text' => $contactInfo['address']],
                            ['icon' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z', 'label' => 'Phone / WhatsApp', 'text' => $contactInfo['phone']],
                            ['icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Business Hours', 'text' => $contactInfo['hours']],
                            ['icon' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m-18.432 0A11.953 11.953 0 0112 13.5c2.998 0 5.74 1.1 7.843 2.918m-15.686 0A8.959 8.959 0 013 12c0-.778.099-1.533.284-2.253', 'label' => 'Website', 'text' => $contactInfo['website']],
                        ];
                    @endphp
                    @foreach($contactItems as $i => $item)
                        <div data-reveal data-reveal-delay="{{ 100 + $i * 80 }}" class="flex items-start gap-4 p-4 rounded-xl bg-white transition-all duration-300 hover:shadow-[0_8px_24px_rgba(139,105,20,0.1)] hover:-translate-y-0.5" style="border: 1px solid #EAE2CE;">
                            <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, #F7EFDB, #EFE2BE);">
                                <svg class="w-5 h-5" style="color: #A67922;" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                            </span>
                            <div>
                                <div class="text-xs font-semibold mb-0.5" style="color: #1A1A1A;">{{ $item['label'] }}</div>
                                <div class="text-[13px] leading-relaxed" style="color: #6B6B6B;">{!! $item['text'] !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Form --}}
            <div data-reveal="right" data-reveal-delay="120" class="bg-white rounded-2xl p-6 sm:p-8 shadow-[0_12px_40px_rgba(139,105,20,0.08)]" style="border: 1px solid #EAE2CE;">
                <h3 class="font-display text-2xl font-semibold mb-1" style="color: #1A1A1A;">Send an Enquiry</h3>
                <p class="text-xs mb-6" style="color: #8A8270;">We usually respond within the hour during business time.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: #5C5341;">Full Name</label>
                        <input type="text" placeholder="Your name" class="w-full px-3.5 py-2.5 rounded-xl text-sm transition-all focus:outline-none" style="border: 1px solid #DDD3BC; color: #1A1A1A; background: #FDFBF6;"
                               onfocus="this.style.borderColor='#C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.12)'"
                               onblur="this.style.borderColor='#DDD3BC'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: #5C5341;">Phone Number</label>
                        <input type="tel" placeholder="03xx xxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl text-sm transition-all focus:outline-none" style="border: 1px solid #DDD3BC; color: #1A1A1A; background: #FDFBF6;"
                               onfocus="this.style.borderColor='#C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.12)'"
                               onblur="this.style.borderColor='#DDD3BC'; this.style.boxShadow='none'">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium mb-1.5" style="color: #5C5341;">Service Required</label>
                    <div class="relative">
                        <select class="w-full appearance-none px-3.5 py-2.5 pr-10 rounded-xl text-sm transition-all focus:outline-none cursor-pointer" style="border: 1px solid #DDD3BC; color: #1A1A1A; background: #FDFBF6;"
                                onfocus="this.style.borderColor='#C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.12)'"
                                onblur="this.style.borderColor='#DDD3BC'; this.style.boxShadow='none'">
                            <option>Gold Products</option>
                            <option>Silver Products</option>
                            <option>Custom Jewelry</option>
                        </select>
                        <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color: #A67922;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-medium mb-1.5" style="color: #5C5341;">Message</label>
                    <textarea rows="3" placeholder="How can we help you?" class="w-full px-3.5 py-2.5 rounded-xl text-sm resize-none transition-all focus:outline-none" style="border: 1px solid #DDD3BC; color: #1A1A1A; background: #FDFBF6;"
                              onfocus="this.style.borderColor='#C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.12)'"
                              onblur="this.style.borderColor='#DDD3BC'; this.style.boxShadow='none'"></textarea>
                </div>
                <button class="group flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm text-white font-semibold transition-all duration-300 hover:shadow-[0_10px_26px_rgba(13,61,31,0.35)] hover:-translate-y-0.5" style="background: linear-gradient(135deg, #0D3D1F, #165A42);">
                    Send Enquiry
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                </button>
            </div>
        </div>
    </section>

</div>
