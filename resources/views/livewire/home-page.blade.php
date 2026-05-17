<div>

    {{-- ================================================================ --}}
    {{-- HERO — Editorial luxury layout (Eluxee-inspired)                   --}}
    {{-- ================================================================ --}}
    <section class="editorial-hero">

        {{-- Soft radial wash + subtle grain --}}
        <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(900px 600px at 70% 30%, rgba(232,201,106,0.18) 0%, transparent 60%); z-index: 0;"></div>

        {{-- Top meta strip --}}
        <div class="relative max-w-7xl mx-auto px-6 sm:px-10 pt-10 sm:pt-14 flex items-center justify-between" style="z-index: 2;">
            <div class="editorial-eyebrow">
                <span class="live-dot" style="width: 6px; height: 6px;"></span>
                <span>Vol. 09 · Est. 2015</span>
            </div>
            <div class="hidden md:flex items-center gap-3 text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">
                <span>Islamabad</span><span>·</span><span>Pakistan</span>
            </div>
        </div>

        {{-- Two-column editorial split --}}
        <div class="relative max-w-7xl mx-auto px-6 sm:px-10 pt-16 sm:pt-20 pb-32 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center" style="z-index: 2;">

            {{-- Left: editorial headline --}}
            <div class="lg:col-span-7" data-reveal-stagger="140">
                <div class="editorial-eyebrow no-rules mb-8" style="color: var(--gold-700);">
                    <span class="w-12 h-px" style="background: var(--gold-500);"></span>
                    Pakistan's Premier Bullion House
                </div>

                <h1 class="editorial-h1">
                    Where gold<br>
                    becomes a <em data-text-reveal>legacy</em>.
                </h1>

                <p class="editorial-lede mt-8">
                    Hand-selected bullion. Live-rate transparency. A decade of trust forged
                    in Islamabad — now refined for the way the world buys gold today.
                </p>

                <div class="flex flex-wrap gap-3 mt-10">
                    <a href="/buy" data-magnetic="0.18" class="editorial-btn">
                        Buy Gold
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-6-6m6 6l-6 6"/></svg>
                    </a>
                    <a href="/live" data-magnetic="0.18" class="editorial-btn editorial-btn--ghost">
                        Live Rates
                    </a>
                </div>

                {{-- Trust line with thin gold rules --}}
                <div class="mt-14 grid grid-cols-3 max-w-md gap-px" style="background: rgba(139, 105, 20, 0.18); border: 1px solid rgba(139, 105, 20, 0.18);">
                    <div class="bg-[var(--ivory)] px-5 py-4">
                        <div class="font-display text-3xl" style="color: var(--ink);" data-counter="10" data-counter-suffix="+">10+</div>
                        <div class="text-[10px] tracking-[0.25em] uppercase mt-1" style="color: var(--gold-700);">Years</div>
                    </div>
                    <div class="bg-[var(--ivory)] px-5 py-4">
                        <div class="font-display text-3xl" style="color: var(--ink);" data-counter="50000" data-counter-format="compact">50K+</div>
                        <div class="text-[10px] tracking-[0.25em] uppercase mt-1" style="color: var(--gold-700);">Clients</div>
                    </div>
                    <div class="bg-[var(--ivory)] px-5 py-4">
                        <div class="font-display text-3xl" style="color: var(--ink);" data-counter="100" data-counter-suffix="%">100%</div>
                        <div class="text-[10px] tracking-[0.25em] uppercase mt-1" style="color: var(--gold-700);">Hallmark</div>
                    </div>
                </div>
            </div>

            {{-- Right: the bar + floating annotations + spot prices --}}
            <div class="lg:col-span-5 relative" style="min-height: 520px;">

                {{-- Decorative frame line --}}
                <div class="absolute inset-x-0 top-6 bottom-6 border" style="border-color: rgba(139,105,20,0.2);"></div>
                <div class="absolute" style="top: 0; left: 50%; width: 1px; height: 32px; background: var(--gold-500); transform: translateX(-50%);"></div>
                <div class="absolute" style="bottom: 0; left: 50%; width: 1px; height: 32px; background: var(--gold-500); transform: translateX(-50%);"></div>

                {{-- Gold bar pedestal --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <div data-three-bar style="width: 360px; height: 260px;"></div>
                    <div class="gold-bar-stage" data-three-bar-fallback>
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
                </div>

                {{-- Floating annotations (Eluxee "detail parts" treatment) --}}
                <div class="annotation" style="top: 8%; left: 0;">
                    <span class="annotation__dot"></span>
                    <span class="annotation__line"></span>
                    <span class="annotation__label"><strong>24K</strong> · 999.9 Fine</span>
                </div>
                <div class="annotation annotation--right" style="top: 30%; right: 0;">
                    <span class="annotation__dot"></span>
                    <span class="annotation__line"></span>
                    <span class="annotation__label"><strong>ARY</strong> Verified</span>
                </div>
                <div class="annotation" style="bottom: 28%; left: 0;">
                    <span class="annotation__dot"></span>
                    <span class="annotation__line"></span>
                    <span class="annotation__label"><strong>1 Tola</strong> · 11.6638 g</span>
                </div>
                <div class="annotation annotation--right" style="bottom: 8%; right: 0;">
                    <span class="annotation__dot"></span>
                    <span class="annotation__line"></span>
                    <span class="annotation__label"><strong>Live</strong> Market Rate</span>
                </div>
            </div>
        </div>

        {{-- Bottom marquee strip with live spot prices --}}
        <div class="relative" style="z-index: 2;">
            <div class="max-w-7xl mx-auto px-6 sm:px-10">
                <hr class="editorial-rule mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-8">
                    <div class="editorial-eyebrow no-rules" style="color: var(--gold-700);">
                        <span class="live-dot" style="width: 6px; height: 6px;"></span>
                        Live Spot Prices
                    </div>
                    <div class="flex flex-wrap items-center gap-8 sm:gap-12">
                        <div class="flex items-baseline gap-3">
                            <span class="font-display text-2xl" style="color: var(--ink);">Gold</span>
                            <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">XAU/USD</span>
                            @if(!empty($internationalRates['xau_usd']))
                                <span class="font-display text-2xl" style="color: var(--gold-700);" data-price="{{ $internationalRates['xau_usd'] }}" data-pkey="intl-xau-bid">${{ number_format($internationalRates['xau_usd'], 2) }}</span>
                            @endif
                        </div>
                        <div class="flex items-baseline gap-3">
                            <span class="font-display text-2xl" style="color: var(--ink);">Silver</span>
                            <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">XAG/USD</span>
                            @if(!empty($internationalRates['xag_usd']))
                                <span class="font-display text-2xl" style="color: var(--ink); opacity: 0.7;" data-price="{{ $internationalRates['xag_usd'] }}" data-pkey="intl-xag-bid">${{ number_format($internationalRates['xag_usd'], 2) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll cue at the very bottom --}}
        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center gap-2 opacity-50">
            <span class="text-[9px] tracking-[0.4em] uppercase" style="color: var(--gold-700);">Scroll</span>
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
    <div class="py-5 px-6 sm:px-10 flex flex-wrap justify-center gap-x-10 gap-y-2 text-[10px] tracking-[0.3em] uppercase" style="background: var(--ivory); border-top: 1px solid rgba(139,105,20,0.18); border-bottom: 1px solid rgba(139,105,20,0.18); color: var(--gold-700);">
        <span>Est. 2015 · 10 yrs of trust</span>
        <span class="hidden sm:inline" style="color: rgba(139,105,20,0.4);">·</span>
        <span>100% Hallmarked</span>
        <span class="hidden sm:inline" style="color: rgba(139,105,20,0.4);">·</span>
        <span>Best Rates in Islamabad</span>
        <span class="hidden md:inline" style="color: rgba(139,105,20,0.4);">·</span>
        <span class="hidden md:inline">Secure Delivery</span>
        <span class="hidden lg:inline" style="color: rgba(139,105,20,0.4);">·</span>
        <span class="hidden lg:inline">Expert Consultation</span>
    </div>

    {{-- ================================================================ --}}
    {{-- ABOUT — editorial double column with timeline                     --}}
    {{-- ================================================================ --}}
    <section class="py-24 sm:py-32 px-6 sm:px-10" style="background: var(--ivory); color: var(--ink);" data-reveal>
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">

            {{-- Left: Establishment + vertical timeline --}}
            <div class="lg:col-span-5">
                <div class="editorial-eyebrow no-rules mb-10" style="color: var(--gold-700);">
                    <span class="w-12 h-px" style="background: var(--gold-500);"></span>
                    Our Story
                </div>

                <div class="flex items-baseline gap-4 mb-2">
                    <div class="font-display" style="font-size: clamp(120px, 18vw, 220px); line-height: 0.9; color: var(--ink);">2015</div>
                </div>
                <div class="text-[11px] tracking-[0.32em] uppercase mt-2" style="color: var(--gold-700);">Established · Islamabad</div>

                {{-- vertical timeline --}}
                <div class="mt-16 space-y-8 relative pl-6">
                    <div class="absolute left-[7px] top-2 bottom-2 w-px" style="background: linear-gradient(180deg, var(--gold-500), transparent);"></div>
                    @foreach([
                        ['2015', 'Founded in F-7 Markaz, Islamabad'],
                        ['2018', 'First international spot-rate ticker live in Pakistan'],
                        ['2021', 'ARY-verified and PNG-hallmarked product line'],
                        ['2024', 'Digital storefront and verified-serial program launched'],
                    ] as $milestone)
                        <div class="relative">
                            <span class="absolute -left-6 top-1.5 w-3.5 h-3.5 rounded-full border-2" style="border-color: var(--gold-500); background: var(--ivory);"></span>
                            <div class="font-display text-2xl" style="color: var(--ink);">{{ $milestone[0] }}</div>
                            <div class="text-sm mt-1" style="color: rgba(26,20,16,0.65);">{{ $milestone[1] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: editorial pull-quote + supporting paragraphs + capability grid --}}
            <div class="lg:col-span-7">
                <h2 class="font-display" style="font-size: clamp(36px, 5vw, 64px); line-height: 1.05; letter-spacing: -0.02em; color: var(--ink);">
                    A decade of <em style="color: var(--gold-700);">craftsmanship</em>,<br>
                    and a quiet obsession with trust.
                </h2>

                <p class="mt-10 text-[15px] leading-[1.85]" style="color: rgba(26,20,16,0.7); max-width: 540px;">
                    Islamabad Bullion Exchange was founded on a single idea: every gram of gold
                    deserves a transparent price and an honest hand. For ten years we have been
                    the address Islamabad's families return to — whether for a wedding set, a
                    weekly bullion trade, or a quiet investment held across generations.
                </p>

                <p class="mt-5 text-[15px] leading-[1.85]" style="color: rgba(26,20,16,0.7); max-width: 540px;">
                    Today, we pair that craft with live international rates, certified hallmarks,
                    and a digital experience built for the way the world buys gold now.
                </p>

                <hr class="editorial-rule my-12">

                {{-- Capability grid: editorial pairs --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-7">
                    @foreach([
                        ['Certified Bullion',     'ARY-verified 24K, 22K and Rawa gold, plus international-grade silver.'],
                        ['Live Spot Pricing',     'Rates refreshed every minute against the XAU/XAG markets.'],
                        ['Bespoke Jewelry',       'Bridal and made-to-order pieces, designed in-house.'],
                        ['Zakat Valuations',      'Nisab-compliant valuations using current Pakistani rates.'],
                        ['Buy-Back Guarantee',    'Sell back to us at live rates — no waiting, no haircuts.'],
                        ['On-Site Gemologist',    'Independent assays available for any piece, anytime.'],
                    ] as [$title, $desc])
                        <div class="flex gap-4">
                            <span class="font-display text-2xl shrink-0" style="color: var(--gold-500); line-height: 1;">·</span>
                            <div>
                                <div class="font-display text-xl" style="color: var(--ink); line-height: 1.2;">{{ $title }}</div>
                                <div class="text-[13px] mt-1.5 leading-relaxed" style="color: rgba(26,20,16,0.6);">{{ $desc }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>


    {{-- ================================================================ --}}
    {{-- LIVE PRICES — editorial "Today's Market" spread                    --}}
    {{-- ================================================================ --}}
    <section class="py-24 sm:py-32 px-6 sm:px-10" style="background: var(--ink); color: var(--ivory);">
        <div class="max-w-7xl mx-auto">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-14" data-reveal>
                <div>
                    <div class="editorial-eyebrow no-rules mb-6" style="color: var(--gold-300);">
                        <span class="w-12 h-px" style="background: var(--gold-300);"></span>
                        Today's Market · {{ now()->format('d M Y') }}
                    </div>
                    <h2 class="font-display" style="font-size: clamp(40px, 6vw, 88px); line-height: 0.95; letter-spacing: -0.02em; color: var(--ivory);" data-text-reveal>
                        Today's Market
                    </h2>
                </div>
                <div class="flex items-center gap-3 text-[11px] tracking-[0.3em] uppercase" style="color: var(--gold-300);">
                    <span class="live-dot" style="width: 7px; height: 7px;"></span>
                    Live · XAU/XAG spot
                </div>
            </div>

            <hr class="editorial-rule mb-12" style="background: linear-gradient(90deg, transparent, var(--gold-300), transparent);">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24" data-reveal-stagger="160">

                {{-- ─── Gold column ─── --}}
                <div>
                    <div class="flex items-baseline justify-between mb-6">
                        <div>
                            <span class="font-display text-3xl" style="color: var(--gold-300);">Gold</span>
                            <span class="ml-3 text-[10px] tracking-[0.3em] uppercase" style="color: rgba(232,201,106,0.55);">24K · PKR</span>
                        </div>
                    </div>

                    {{-- The headline price: 1 Tola, oversize --}}
                    @php
                        $gold1tBuy = $goldPrices['24k']['tola']['buy'] ?? null;
                        $gold1tSell = $goldPrices['24k']['tola']['sell'] ?? null;
                    @endphp
                    <div class="font-display leading-none" style="font-size: clamp(56px, 9vw, 120px); color: var(--ivory); letter-spacing: -0.02em;">
                        @if($gold1tBuy)
                            <span data-price="{{ $gold1tBuy }}" data-pkey="gold-24k-tola-buy">{{ number_format($gold1tBuy) }}</span>
                        @else
                            ---
                        @endif
                    </div>
                    <div class="text-[11px] tracking-[0.3em] uppercase mt-3" style="color: rgba(232,201,106,0.55);">Per Tola · Buy</div>

                    <hr class="editorial-rule my-8" style="background: linear-gradient(90deg, transparent, rgba(232,201,106,0.35), transparent);">

                    {{-- Supporting unit breakdown --}}
                    @php
                        $goldUnits = ['10_gram' => '10 Gram', '5_gram' => '5 Gram', 'gram' => '1 Gram'];
                    @endphp
                    <div class="space-y-5">
                        @foreach($goldUnits as $unit => $label)
                            @php
                                $gBuy = $goldPrices['24k'][$unit]['buy'] ?? null;
                                $gSell = $goldPrices['24k'][$unit]['sell'] ?? null;
                            @endphp
                            <div class="flex items-baseline justify-between gap-6">
                                <div class="text-[11px] tracking-[0.25em] uppercase" style="color: rgba(255,255,255,0.45);">{{ $label }}</div>
                                <div class="flex items-baseline gap-8 font-display">
                                    <span class="text-xl" style="color: var(--ivory);" @if($gBuy) data-price="{{ $gBuy }}" data-pkey="gold-24k-{{ $unit }}-buy" @endif>{{ $gBuy ? number_format($gBuy) : '---' }}</span>
                                    <span class="text-xl opacity-60" style="color: var(--gold-300);" @if($gSell) data-price="{{ $gSell }}" data-pkey="gold-24k-{{ $unit }}-sell" @endif>{{ $gSell ? number_format($gSell) : '---' }}</span>
                                </div>
                            </div>
                        @endforeach
                        <div class="flex justify-between text-[9px] tracking-[0.3em] uppercase pt-3" style="color: rgba(232,201,106,0.45); border-top: 1px solid rgba(232,201,106,0.2);">
                            <span>&nbsp;</span>
                            <div class="flex gap-8"><span>Buy</span><span>Sell</span></div>
                        </div>
                    </div>
                </div>

                {{-- ─── Silver column ─── --}}
                <div>
                    <div class="flex items-baseline justify-between mb-6">
                        <div>
                            <span class="font-display text-3xl" style="color: rgba(255,255,255,0.85);">Silver</span>
                            <span class="ml-3 text-[10px] tracking-[0.3em] uppercase" style="color: rgba(255,255,255,0.4);">XAG · PKR</span>
                        </div>
                    </div>

                    @php
                        $sKgBuy = $silverPrices['24k']['kg']['buy'] ?? null;
                    @endphp
                    <div class="font-display leading-none" style="font-size: clamp(56px, 9vw, 120px); color: var(--ivory); letter-spacing: -0.02em;">
                        @if($sKgBuy)
                            <span data-price="{{ $sKgBuy }}" data-pkey="silver-kg-buy">{{ number_format($sKgBuy) }}</span>
                        @else
                            ---
                        @endif
                    </div>
                    <div class="text-[11px] tracking-[0.3em] uppercase mt-3" style="color: rgba(255,255,255,0.4);">Per 1 KG · Buy</div>

                    <hr class="editorial-rule my-8" style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);">

                    @php
                        $silverUnits = ['10_tola' => '10 Tola', 'tola' => '1 Tola', '10_gram' => '10 Gram'];
                    @endphp
                    <div class="space-y-5">
                        @foreach($silverUnits as $unit => $label)
                            @php
                                $sBuy = $silverPrices['24k'][$unit]['buy'] ?? null;
                                $sSell = $silverPrices['24k'][$unit]['sell'] ?? null;
                            @endphp
                            <div class="flex items-baseline justify-between gap-6">
                                <div class="text-[11px] tracking-[0.25em] uppercase" style="color: rgba(255,255,255,0.45);">{{ $label }}</div>
                                <div class="flex items-baseline gap-8 font-display">
                                    <span class="text-xl" style="color: var(--ivory);" @if($sBuy) data-price="{{ $sBuy }}" data-pkey="silver-{{ $unit }}-buy" @endif>{{ $sBuy ? number_format($sBuy) : '---' }}</span>
                                    <span class="text-xl opacity-60" style="color: rgba(255,255,255,0.65);" @if($sSell) data-price="{{ $sSell }}" data-pkey="silver-{{ $unit }}-sell" @endif>{{ $sSell ? number_format($sSell) : '---' }}</span>
                                </div>
                            </div>
                        @endforeach
                        <div class="flex justify-between text-[9px] tracking-[0.3em] uppercase pt-3" style="color: rgba(255,255,255,0.35); border-top: 1px solid rgba(255,255,255,0.15);">
                            <span>&nbsp;</span>
                            <div class="flex gap-8"><span>Buy</span><span>Sell</span></div>
                        </div>
                    </div>
                </div>

            </div>

            <p class="text-center text-[11px] tracking-[0.3em] uppercase mt-16" style="color: rgba(232,201,106,0.5);">
                Rates refreshed every 60 seconds · Sourced from international spot markets
            </p>
        </div>
    </section>


    {{-- ================================================================ --}}
    {{-- SHOP — editorial mosaic                                           --}}
    {{-- ================================================================ --}}
    <section class="py-24 sm:py-32 px-6 sm:px-10" style="background: var(--ivory); color: var(--ink);" data-reveal>
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12">
                <div>
                    <div class="editorial-eyebrow no-rules mb-6" style="color: var(--gold-700);">
                        <span class="w-12 h-px" style="background: var(--gold-500);"></span>
                        The Collection · Vol. 09
                    </div>
                    <h2 class="font-display" style="font-size: clamp(40px, 6vw, 88px); line-height: 0.95; letter-spacing: -0.02em;" data-text-reveal>Shop the collection</h2>
                </div>
                <a href="/products" class="editorial-btn editorial-btn--ghost" data-magnetic="0.18">
                    View All Pieces
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-6-6m6 6l-6 6"/></svg>
                </a>
            </div>

            <div class="flex flex-wrap items-center gap-x-8 gap-y-3 mb-14 pb-6" style="border-bottom: 1px solid rgba(139, 105, 20, 0.18);">
                <a href="/products" class="text-[11px] tracking-[0.3em] uppercase font-medium" style="color: var(--ink); border-bottom: 1px solid var(--ink); padding-bottom: 2px;">All Pieces</a>
                @foreach($productCategories as $cat)
                    <a href="/products?category={{ $cat->slug }}" class="text-[11px] tracking-[0.3em] uppercase transition-colors" style="color: rgba(26,20,16,0.45);" onmouseover="this.style.color='var(--gold-700)';" onmouseout="this.style.color='rgba(26,20,16,0.45)';">{{ $cat->name }}</a>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8" data-reveal-stagger="140">
                @foreach($products as $i => $prod)
                    @php
                        $prodBg = $prod->metal === 'silver' ? 'linear-gradient(135deg,#E8E5DC,#C8C5BC)' : 'linear-gradient(135deg,#F5DD8E,#C9A84C)';
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
                        $spanClass = match($i) {
                            0 => 'md:col-span-7 md:row-span-2',
                            1 => 'md:col-span-5',
                            2 => 'md:col-span-5',
                            3 => 'md:col-span-7',
                            default => 'md:col-span-6',
                        };
                        $imgHeight = $i === 0 ? 'h-[420px] md:h-[560px]' : 'h-[240px] md:h-[260px]';
                    @endphp
                    <a href="{{ $prodUrl }}" class="group relative {{ $spanClass }} block overflow-hidden bg-white" data-tilt="4" style="border: 1px solid rgba(139,105,20,0.18);">
                        <div class="relative {{ $imgHeight }} overflow-hidden">
                            @if(!$isEnquiry && $prod->discount_label)
                                <span class="absolute top-4 left-4 z-10 px-3 py-1 text-[10px] tracking-[0.25em] uppercase" style="background: var(--ink); color: var(--ivory);">{{ $prod->discount_label }}</span>
                            @endif
                            @if($prod->image)
                                <img src="{{ Storage::disk('public')->url($prod->image) }}" alt="{{ $prod->name }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1200ms] group-hover:scale-105">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center" style="background: {{ $prodBg }};">
                                    <span class="font-display" style="font-size: 80px; color: rgba(26,20,16,0.18);">{{ $prod->metal === 'silver' ? 'Ag' : 'Au' }}</span>
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 px-6 py-4 flex items-end justify-between gap-4 translate-y-full group-hover:translate-y-0 transition-transform duration-500"
                                 style="background: linear-gradient(0deg, rgba(26,20,16,0.92), rgba(26,20,16,0));">
                                <div class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-300);">View piece →</div>
                            </div>
                        </div>

                        <div class="p-6 sm:p-8 flex flex-col gap-3">
                            <div>
                                <div class="text-[10px] tracking-[0.3em] uppercase mb-2" style="color: var(--gold-700);">{{ $prod->productCategory?->name ?? ucfirst($prod->metal) }}</div>
                                <div class="font-display text-2xl" style="color: var(--ink); line-height: 1.1;">{{ $prod->name }}</div>
                                <div class="text-[12px] mt-1.5" style="color: rgba(26,20,16,0.5);">{{ $prod->weight }}</div>
                            </div>

                            <div class="flex items-baseline justify-between gap-4 mt-3 pt-4" style="border-top: 1px solid rgba(139,105,20,0.18);">
                                <div>
                                    @if($isEnquiry)
                                        <div class="font-display text-xl" style="color: var(--ink);">Get a Quote</div>
                                    @elseif($prod->price_type === 'fixed' && $prod->fixed_price)
                                        @if($prod->hasActiveDiscount())
                                            <div class="text-[11px] line-through" style="color: rgba(26,20,16,0.4);">Rs {{ number_format($prod->fixed_price) }}</div>
                                            <div class="font-display text-2xl" style="color: var(--gold-700);">Rs {{ number_format($prod->applyDiscount($prod->fixed_price)) }}</div>
                                        @else
                                            <div class="font-display text-2xl" style="color: var(--ink);">Rs {{ number_format($prod->fixed_price) }}</div>
                                        @endif
                                    @elseif($livePrice)
                                        @if($prod->hasActiveDiscount())
                                            <div class="text-[11px] line-through" style="color: rgba(26,20,16,0.4);">Rs {{ number_format($livePrice) }}</div>
                                            <div class="font-display text-2xl" style="color: var(--gold-700);">Rs {{ number_format($prod->applyDiscount($livePrice)) }}</div>
                                        @else
                                            <div class="font-display text-2xl" style="color: var(--ink);">Rs {{ number_format($livePrice) }}</div>
                                        @endif
                                    @else
                                        <div class="font-display text-xl italic" style="color: rgba(26,20,16,0.55);">Contact for price</div>
                                    @endif
                                </div>
                                <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--ink);">
                                    {{ $isEnquiry ? 'Enquire' : 'Buy' }} →
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ================================================================ --}}
    {{-- SERVICES — numbered editorial list                                --}}
    {{-- ================================================================ --}}
    <section class="py-24 sm:py-32 px-6 sm:px-10" style="background: var(--bone); color: var(--ink);" data-reveal>
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8 mb-16">
                <div>
                    <div class="editorial-eyebrow no-rules mb-6" style="color: var(--gold-700);">
                        <span class="w-12 h-px" style="background: var(--gold-500);"></span>
                        Services · 01–{{ str_pad((string) max(1, $services->count()), 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <h2 class="font-display" style="font-size: clamp(40px, 6vw, 88px); line-height: 0.95; letter-spacing: -0.02em;" data-text-reveal>What we do best</h2>
                </div>
                <p class="font-display-italic max-w-md" style="font-size: clamp(18px, 1.5vw, 22px); color: rgba(26,20,16,0.55); line-height: 1.5;">
                    Every service we offer follows one principle — transparency, in price and in process.
                </p>
            </div>

            <hr class="editorial-rule mb-0">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-16" data-reveal-stagger="120">
                @foreach($services as $i => $svc)
                    <div class="group flex items-start gap-8 py-10 transition-colors hover:bg-[var(--ivory)]" style="border-bottom: 1px solid rgba(139,105,20,0.18); margin-left: -16px; padding-left: 16px;">
                        <div class="font-display flex-shrink-0" style="font-size: clamp(40px, 4.5vw, 64px); color: var(--gold-500); line-height: 1; width: 90px; opacity: 0.6;">
                            {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="flex-1 pt-2">
                            <h3 class="font-display text-3xl" style="color: var(--ink); line-height: 1.15;">{{ $svc->title }}</h3>
                            <p class="font-display-italic mt-3 text-lg leading-relaxed" style="color: rgba(26,20,16,0.62);">{{ $svc->description }}</p>
                        </div>
                        <span class="self-center text-[10px] tracking-[0.3em] uppercase opacity-0 group-hover:opacity-100 transition-opacity" style="color: var(--gold-700);">→</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ================================================================ --}}
    {{-- ZAKAT CALCULATOR SECTION                                          --}}
    {{-- ================================================================ --}}
    <section class="py-24 sm:py-32 px-6 sm:px-10" style="background: var(--bone);" data-reveal>
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            <div class="lg:col-span-7">
                <div class="editorial-eyebrow no-rules mb-6" style="color: var(--gold-700);">
                    <span class="w-12 h-px" style="background: var(--gold-500);"></span>
                    Islamic Finance Tool
                </div>
                <h2 class="font-display" style="font-size: clamp(40px, 6vw, 88px); line-height: 0.96; letter-spacing: -0.02em; color: var(--ink);" data-text-reveal>
                    Calculate your <em style="color: var(--gold-700);">Zakat</em>.
                </h2>
                <p class="font-display-italic mt-6 text-xl leading-relaxed" style="color: rgba(26,20,16,0.6); max-width: 520px;">
                    A Nisab-compliant valuation, based on the day's live market — no spreadsheets, no guesswork.
                </p>
                <p class="text-[12px] tracking-[0.18em] uppercase mt-6" style="color: var(--gold-700);">
                    Current Nisab · 7.5 tola gold or 52.5 tola silver
                </p>
            </div>
            <div class="lg:col-span-5">
                <div class="p-8 sm:p-10 bg-white" style="border: 1px solid rgba(139,105,20,0.18);">
                    <div class="mb-6">
                        <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">Gold Weight</span>
                        <input type="number" placeholder="e.g. 10 tolas"
                               class="block w-full mt-2 pb-2 font-display text-2xl bg-transparent outline-none focus:border-[var(--gold-700)] transition-colors"
                               style="border: 0; border-bottom: 1px solid rgba(26,20,16,0.25); color: var(--ink);">
                    </div>
                    <div class="mb-8">
                        <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">Silver Weight</span>
                        <input type="number" placeholder="e.g. 50 tolas"
                               class="block w-full mt-2 pb-2 font-display text-2xl bg-transparent outline-none focus:border-[var(--gold-700)] transition-colors"
                               style="border: 0; border-bottom: 1px solid rgba(26,20,16,0.25); color: var(--ink);">
                    </div>
                    <a href="/zakat-calculator" class="editorial-btn w-full justify-center" data-magnetic="0.18">
                        Calculate Zakat
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-6-6m6 6l-6 6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>


    {{-- ================================================================ --}}
    {{-- TESTIMONIALS SECTION                                              --}}
    {{-- ================================================================ --}}
    @livewire('testimonial-section')


    {{-- ================================================================ --}}
    {{-- STATS / WHY US — full-width serif spread                          --}}
    {{-- ================================================================ --}}
    <section class="relative py-28 sm:py-36 px-6 sm:px-10 overflow-hidden" style="background: var(--espresso); color: var(--ivory);" data-reveal>
        {{-- Decorative serif watermark behind --}}
        <div class="absolute inset-0 pointer-events-none flex items-center justify-center" style="z-index: 0;">
            <span class="font-display" style="font-size: clamp(280px, 50vw, 720px); color: rgba(232, 201, 106, 0.04); letter-spacing: -0.04em; line-height: 0.85;">Trust</span>
        </div>

        {{-- Subtle gold mesh --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.04]" style="z-index: 0; background-image: linear-gradient(rgba(232,201,106,0.6) 1px, transparent 1px), linear-gradient(90deg, rgba(232,201,106,0.6) 1px, transparent 1px); background-size: 80px 80px;"></div>

        <div class="relative max-w-7xl mx-auto" style="z-index: 1;">
            <div class="editorial-eyebrow no-rules mb-8" style="color: var(--gold-300);">
                <span class="w-12 h-px" style="background: var(--gold-300);"></span>
                Track Record
            </div>
            <h2 class="font-display max-w-3xl mb-16" style="font-size: clamp(40px, 6vw, 88px); line-height: 0.98; letter-spacing: -0.02em; color: var(--ivory);" data-text-reveal>
                A decade. Tens of thousands of clients. One standard.
            </h2>

            {{-- Four big serif numbers, each one a hero --}}
            @php
                $statBlocks = [
                    ['num' => $trackRecord['years'],     'label' => 'Years in Business', 'detail' => 'Continuous trading since 2015'],
                    ['num' => $trackRecord['customers'], 'label' => 'Clients Served',    'detail' => 'And counting, across Pakistan'],
                    ['num' => $trackRecord['authentic'], 'label' => 'Authenticity',      'detail' => 'Every piece hallmark-verified'],
                    ['num' => $trackRecord['rating'],    'label' => 'Google Rating',     'detail' => 'From real, verified reviews'],
                ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-16 gap-x-12" data-reveal-stagger="160">
                @foreach($statBlocks as $stat)
                    <div class="flex items-baseline gap-6 lg:gap-10 pb-10" style="border-bottom: 1px solid rgba(232,201,106,0.15);">
                        <div class="font-display flex-shrink-0" style="font-size: clamp(80px, 12vw, 180px); color: var(--gold-300); line-height: 0.85; letter-spacing: -0.03em;">{{ $stat['num'] }}</div>
                        <div class="flex-1 pb-2">
                            <div class="font-display text-2xl mb-2" style="color: var(--ivory); line-height: 1.1;">{{ $stat['label'] }}</div>
                            <div class="text-[12px] tracking-[0.18em] uppercase" style="color: rgba(232,201,106,0.55);">{{ $stat['detail'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ================================================================ --}}
    {{-- CONTACT — editorial split with serif address                      --}}
    {{-- ================================================================ --}}
    <section class="py-24 sm:py-32 px-6 sm:px-10" style="background: var(--ivory); color: var(--ink);" data-reveal>
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">

            {{-- Left: Editorial contact "card" --}}
            <div class="lg:col-span-5">
                <div class="editorial-eyebrow no-rules mb-6" style="color: var(--gold-700);">
                    <span class="w-12 h-px" style="background: var(--gold-500);"></span>
                    Visit · Call · Write
                </div>

                <h2 class="font-display" style="font-size: clamp(40px, 6vw, 72px); line-height: 0.98; letter-spacing: -0.02em;" data-text-reveal>
                    Come find us<br>in Islamabad.
                </h2>

                <p class="font-display-italic mt-6 text-xl leading-relaxed" style="color: rgba(26,20,16,0.6); max-width: 420px;">
                    A walk-in welcome, a same-day quote, a phone call answered in person — the old-fashioned way.
                </p>

                <hr class="editorial-rule my-10">

                <div class="space-y-8">
                    @php
                        $contactItems = [
                            ['label' => 'Address',           'text' => $contactInfo['address']],
                            ['label' => 'Phone · WhatsApp',  'text' => $contactInfo['phone']],
                            ['label' => 'Hours',             'text' => $contactInfo['hours']],
                            ['label' => 'Online',            'text' => $contactInfo['website']],
                        ];
                    @endphp
                    @foreach($contactItems as $item)
                        <div>
                            <div class="text-[10px] tracking-[0.3em] uppercase mb-2" style="color: var(--gold-700);">{{ $item['label'] }}</div>
                            <div class="font-display text-xl leading-snug" style="color: var(--ink);">{!! $item['text'] !!}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Form with underline inputs --}}
            <div class="lg:col-span-7 lg:pl-12">
                <div class="p-8 sm:p-12 bg-white" style="border: 1px solid rgba(139,105,20,0.18);">
                    <div class="editorial-eyebrow no-rules mb-6" style="color: var(--gold-700);">
                        <span class="w-12 h-px" style="background: var(--gold-500);"></span>
                        Send an Enquiry
                    </div>
                    <h3 class="font-display text-3xl mb-10" style="color: var(--ink); line-height: 1.1;">Tell us how we can help.</h3>

                    <form action="/contact" method="POST" class="space-y-8">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-8">
                            <label class="block">
                                <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">Full Name</span>
                                <input type="text" name="name" placeholder="Your name"
                                       class="block w-full mt-2 pb-2 text-base bg-transparent outline-none focus:border-[var(--gold-700)] transition-colors"
                                       style="border: 0; border-bottom: 1px solid rgba(26,20,16,0.25); color: var(--ink);">
                            </label>
                            <label class="block">
                                <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">Phone Number</span>
                                <input type="text" name="phone" placeholder="03xx xxxxxxx"
                                       class="block w-full mt-2 pb-2 text-base bg-transparent outline-none focus:border-[var(--gold-700)] transition-colors"
                                       style="border: 0; border-bottom: 1px solid rgba(26,20,16,0.25); color: var(--ink);">
                            </label>
                        </div>

                        <label class="block">
                            <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">Service Required</span>
                            <select name="service"
                                    class="block w-full mt-2 pb-2 text-base bg-transparent outline-none"
                                    style="border: 0; border-bottom: 1px solid rgba(26,20,16,0.25); color: var(--ink);">
                                <option>Buy Gold</option>
                                <option>Sell Gold</option>
                                <option>Buy Silver</option>
                                <option>Sell Silver</option>
                                <option>Custom Jewelry</option>
                                <option>Zakat Valuation</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-[10px] tracking-[0.3em] uppercase" style="color: var(--gold-700);">Message</span>
                            <textarea name="message" rows="3" placeholder="How can we help you?"
                                      class="block w-full mt-2 pb-2 text-base bg-transparent outline-none resize-none focus:border-[var(--gold-700)] transition-colors"
                                      style="border: 0; border-bottom: 1px solid rgba(26,20,16,0.25); color: var(--ink);"></textarea>
                        </label>

                        <button type="submit" class="editorial-btn mt-4" data-magnetic="0.18">
                            Send Enquiry
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-6-6m6 6l-6 6"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

</div>
