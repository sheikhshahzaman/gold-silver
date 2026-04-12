<div wire:poll.3s="refresh">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- ============================================================== --}}
        {{-- Section A: International Metal Rates (dark card for contrast) --}}
        {{-- ============================================================== --}}
        <section class="dark-card p-5 sm:p-6" wire:key="international-rates">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gold flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                        International Metal Rates
                    </h2>
                    <div class="h-0.5 w-16 bg-gradient-to-r from-gold to-transparent mt-1 rounded-full"></div>
                </div>
                <div class="flex items-center gap-2 text-xs text-green-400">
                    <span class="live-dot"></span>
                    <span class="font-semibold">LIVE</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left py-3 px-3 text-white/40 font-medium uppercase text-xs tracking-wider">Metal (per oz)</th>
                            <th class="text-right py-3 px-3 text-white/40 font-medium uppercase text-xs tracking-wider">BID</th>
                            <th class="text-right py-3 px-3 text-white/40 font-medium uppercase text-xs tracking-wider">ASK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $goldQuote = $this->goldQuote;
                            $silverQuote = $this->silverQuote;
                        @endphp
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: linear-gradient(135deg, #C6963C, #A67922); color: white;">Au</span>
                                    <div>
                                        <span class="font-semibold text-white">Gold</span>
                                        <span class="text-white/40 text-xs ml-1">(XAU)</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right py-3.5 px-3">
                                @if($goldQuote)
                                    <span class="text-gold font-bold text-base" data-price="{{ $goldQuote['bid'] }}" data-pkey="intl-xau-bid">${{ number_format($goldQuote['bid'], 2) }}</span>
                                @else
                                    <span class="text-white/20">&mdash;</span>
                                @endif
                            </td>
                            <td class="text-right py-3.5 px-3">
                                @if($goldQuote)
                                    <span class="text-gold font-bold text-base" data-price="{{ $goldQuote['ask'] }}" data-pkey="intl-xau-ask">${{ number_format($goldQuote['ask'], 2) }}</span>
                                @else
                                    <span class="text-white/20">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-full bg-gray-500/30 flex items-center justify-center text-gray-200 text-sm font-bold">Ag</span>
                                    <div>
                                        <span class="font-semibold text-white">Silver</span>
                                        <span class="text-white/40 text-xs ml-1">(XAG)</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right py-3.5 px-3">
                                @if($silverQuote)
                                    <span class="text-white font-bold text-base" data-price="{{ $silverQuote['bid'] }}" data-pkey="intl-xag-bid">${{ number_format($silverQuote['bid'], 2) }}</span>
                                @else
                                    <span class="text-white/20">&mdash;</span>
                                @endif
                            </td>
                            <td class="text-right py-3.5 px-3">
                                @if($silverQuote)
                                    <span class="text-white font-bold text-base" data-price="{{ $silverQuote['ask'] }}" data-pkey="intl-xag-ask">${{ number_format($silverQuote['ask'], 2) }}</span>
                                @else
                                    <span class="text-white/20">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($lastUpdated)
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center gap-1.5 text-xs text-white/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Updated {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }}
                </div>
            @endif
        </section>

        {{-- ============================================================== --}}
        {{-- Grid: Gold + Silver side by side (LIGHT CARDS) --}}
        {{-- ============================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Section B: Gold Rates (PKR) - LIGHT CARD --}}
            <section class="glass-card p-5 sm:p-6" wire:key="gold-rates">
                <div class="mb-5">
                    <h2 class="text-lg sm:text-xl font-bold flex items-center gap-2" style="color: #0A2E23;">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" style="background: linear-gradient(135deg, #C6963C, #A67922); color: white;">Au</span>
                        Gold (XAU)
                    </h2>
                    <div class="h-0.5 w-16 bg-gradient-to-r from-gold to-transparent mt-1 rounded-full"></div>
                </div>

                {{-- Tab Selector --}}
                <div class="flex flex-wrap gap-2 mb-5">
                    @foreach($this->goldTabs as $unitKey => $unitLabel)
                        <button
                            wire:click="selectUnit('{{ $unitKey }}')"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                                {{ $selectedUnit === $unitKey
                                    ? 'text-white shadow-md'
                                    : 'text-emerald-900/70 border border-emerald-900/20 hover:border-gold/50 hover:text-gold-dark bg-white' }}"
                            @if($selectedUnit === $unitKey) style="background: linear-gradient(135deg, #0F3D2E, #0A2E23);" @endif
                        >
                            {{ $unitLabel }}
                        </button>
                    @endforeach
                </div>

                {{-- Price Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 2px solid #E8DFD0;">
                                <th class="text-left py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #0A2E23;">Gold</th>
                                <th class="text-right py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #1B5E20;">Buy</th>
                                <th class="text-right py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #E65100;">Sell</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $karatLabels = ['24k' => '24K', 'rawa' => 'Rawa', '22k' => '22K', '21k' => '21K', '18k' => '18K'];
                            @endphp
                            @foreach($karatLabels as $karat => $label)
                                @php
                                    $buyPrice = $goldPrices[$karat][$selectedUnit]['buy'] ?? null;
                                    $sellPrice = $goldPrices[$karat][$selectedUnit]['sell'] ?? null;
                                @endphp
                                <tr class="border-b transition-colors hover:bg-cream/50" style="border-color: #F0E8DB;" wire:key="gold-{{ $karat }}-{{ $selectedUnit }}">
                                    <td class="py-3.5 px-3">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold" style="color: #0A2E23;">{{ $label }}</span>
                                            <span class="text-xs" style="color: #999;">({{ $this->selectedUnitLabel }})</span>
                                        </div>
                                    </td>
                                    <td class="text-right py-3.5 px-3">
                                        @if($buyPrice !== null)
                                            <span class="price-badge-buy" data-price="{{ $buyPrice }}" data-pkey="gold-{{ $karat }}-{{ $selectedUnit }}-buy">Rs {{ number_format($buyPrice) }}</span>
                                        @else
                                            <span style="color: #ccc;">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="text-right py-3.5 px-3">
                                        @if($sellPrice !== null)
                                            <span class="price-badge-sell" data-price="{{ $sellPrice }}" data-pkey="gold-{{ $karat }}-{{ $selectedUnit }}-sell">Rs {{ number_format($sellPrice) }}</span>
                                        @else
                                            <span style="color: #ccc;">&mdash;</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-xs italic" style="color: #999;">
                    Prices calculated for {{ $this->selectedUnitLabel }} quantity
                </p>

                @if($lastUpdated)
                    <div class="mt-3 pt-3 flex items-center gap-1.5 text-xs" style="border-top: 1px solid #F0E8DB; color: #999;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Updated {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }}
                    </div>
                @endif
            </section>

            {{-- Section C: Silver Rates (PKR) - LIGHT CARD --}}
            <section class="glass-card p-5 sm:p-6" wire:key="silver-rates">
                <div class="mb-5">
                    <h2 class="text-lg sm:text-xl font-bold flex items-center gap-2" style="color: #0A2E23;">
                        <span class="w-7 h-7 rounded-full bg-gray-400/30 flex items-center justify-center text-gray-600 text-xs font-bold">Ag</span>
                        Silver (XAG)
                    </h2>
                    <div class="h-0.5 w-16 bg-gradient-to-r from-gray-400 to-transparent mt-1 rounded-full"></div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 2px solid #E8DFD0;">
                                <th class="text-left py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #0A2E23;">Silver</th>
                                <th class="text-right py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #1B5E20;">Buy</th>
                                <th class="text-right py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #E65100;">Sell</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $silverUnitLabels = ['kg' => '1 KG', '10_tola' => '10 Tola', 'tola' => '1 Tola', '10_gram' => '10 Gram', 'gram' => '1 Gram'];
                            @endphp
                            @foreach($silverUnitLabels as $unit => $label)
                                @php
                                    $buyPrice = $silverPrices[$unit]['buy'] ?? null;
                                    $sellPrice = $silverPrices[$unit]['sell'] ?? null;
                                @endphp
                                <tr class="border-b transition-colors hover:bg-cream/50" style="border-color: #F0E8DB;" wire:key="silver-{{ $unit }}">
                                    <td class="py-3.5 px-3">
                                        <span class="font-bold" style="color: #0A2E23;">{{ $label }}</span>
                                    </td>
                                    <td class="text-right py-3.5 px-3">
                                        @if($buyPrice !== null)
                                            <span class="price-badge-buy" data-price="{{ $buyPrice }}" data-pkey="silver-{{ $unit }}-buy">Rs {{ number_format($buyPrice) }}</span>
                                        @else
                                            <span style="color: #ccc;">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="text-right py-3.5 px-3">
                                        @if($sellPrice !== null)
                                            <span class="price-badge-sell" data-price="{{ $sellPrice }}" data-pkey="silver-{{ $unit }}-sell">Rs {{ number_format($sellPrice) }}</span>
                                        @else
                                            <span style="color: #ccc;">&mdash;</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($lastUpdated)
                    <div class="mt-4 pt-3 flex items-center gap-1.5 text-xs" style="border-top: 1px solid #F0E8DB; color: #999;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Updated {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }}
                    </div>
                @endif
            </section>

        </div>

        {{-- ============================================================== --}}
        {{-- Section D: Other Commodities (3-column grid of glass-cards)    --}}
        {{-- ============================================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Platinum Card --}}
            <section class="glass-card p-5 sm:p-6" wire:key="platinum-rates">
                <div class="mb-4">
                    <h2 class="text-lg font-bold flex items-center gap-2" style="color: #0A2E23;">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" style="background: linear-gradient(135deg, #B0BEC5, #78909C); color: white;">Pt</span>
                        Platinum
                    </h2>
                    <div class="h-0.5 w-12 bg-gradient-to-r from-gray-400 to-transparent mt-1 rounded-full"></div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #999;">International (USD/oz)</p>
                        @if(!empty($platinumRates['international']))
                            <p class="text-2xl font-bold" style="color: #0A2E23;" data-price="{{ $platinumRates['international'] }}" data-pkey="platinum-intl">${{ number_format($platinumRates['international'], 2) }}</p>
                        @else
                            <p class="text-2xl font-bold" style="color: #ccc;">&mdash;</p>
                        @endif
                    </div>
                    <div style="border-top: 1px solid #F0E8DB; padding-top: 0.75rem;">
                        <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #999;">Local (PKR/tola)</p>
                        @if(!empty($platinumRates['local']))
                            <p class="text-2xl font-bold" style="color: #0A2E23;" data-price="{{ $platinumRates['local'] }}" data-pkey="platinum-local">Rs {{ number_format($platinumRates['local']) }}</p>
                        @else
                            <p class="text-2xl font-bold" style="color: #ccc;">&mdash;</p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Palladium Card --}}
            <section class="glass-card p-5 sm:p-6" wire:key="palladium-rates">
                <div class="mb-4">
                    <h2 class="text-lg font-bold flex items-center gap-2" style="color: #0A2E23;">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" style="background: linear-gradient(135deg, #90A4AE, #546E7A); color: white;">Pd</span>
                        Palladium
                    </h2>
                    <div class="h-0.5 w-12 bg-gradient-to-r from-gray-400 to-transparent mt-1 rounded-full"></div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #999;">International (USD/oz)</p>
                        @if(!empty($palladiumRates['international']))
                            <p class="text-2xl font-bold" style="color: #0A2E23;" data-price="{{ $palladiumRates['international'] }}" data-pkey="palladium-intl">${{ number_format($palladiumRates['international'], 2) }}</p>
                        @else
                            <p class="text-2xl font-bold" style="color: #ccc;">&mdash;</p>
                        @endif
                    </div>
                    <div style="border-top: 1px solid #F0E8DB; padding-top: 0.75rem;">
                        <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #999;">Local (PKR/tola)</p>
                        @if(!empty($palladiumRates['local']))
                            <p class="text-2xl font-bold" style="color: #0A2E23;" data-price="{{ $palladiumRates['local'] }}" data-pkey="palladium-local">Rs {{ number_format($palladiumRates['local']) }}</p>
                        @else
                            <p class="text-2xl font-bold" style="color: #ccc;">&mdash;</p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Market Info Card (Crude Oil + PSX) --}}
            <section class="glass-card p-5 sm:p-6" wire:key="market-info">
                <div class="mb-4">
                    <h2 class="text-lg font-bold flex items-center gap-2" style="color: #0A2E23;">
                        <svg class="w-5 h-5" style="color: #C6963C;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                        Market Info
                    </h2>
                    <div class="h-0.5 w-12 bg-gradient-to-r from-gold to-transparent mt-1 rounded-full"></div>
                </div>

                <div class="space-y-4">
                    {{-- Crude Oil --}}
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #999;">Crude Oil (USD/barrel)</p>
                        @if(!empty($crudeOilPrice))
                            <p class="text-2xl font-bold" style="color: #0A2E23;" data-price="{{ $crudeOilPrice }}" data-pkey="crude-oil">${{ number_format($crudeOilPrice, 2) }}</p>
                        @else
                            <p class="text-2xl font-bold" style="color: #ccc;">&mdash;</p>
                        @endif
                    </div>

                    {{-- PSX / KSE --}}
                    <div style="border-top: 1px solid #F0E8DB; padding-top: 0.75rem;">
                        <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #999;">PSX (KSE-100 Index)</p>
                        @if(!empty($psxData['index']))
                            <p class="text-2xl font-bold" style="color: #0A2E23;" data-price="{{ $psxData['index'] }}" data-pkey="psx-index">{{ number_format($psxData['index'], 2) }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                @php
                                    $psxChange = $psxData['change'] ?? 0;
                                    $isPositive = $psxChange >= 0;
                                @endphp
                                <span class="text-sm font-semibold {{ $isPositive ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $isPositive ? '+' : '' }}{{ number_format($psxChange, 2) }}
                                </span>
                                @if(!empty($psxData['high']) && !empty($psxData['low']))
                                    <span class="text-xs" style="color: #999;">
                                        H: {{ number_format($psxData['high'], 2) }} | L: {{ number_format($psxData['low'], 2) }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="text-2xl font-bold" style="color: #ccc;">&mdash;</p>
                        @endif
                    </div>
                </div>
            </section>

        </div>

        {{-- ============================================================== --}}
        {{-- Section E: Currency Exchange Rates - GOLD TINTED CARD          --}}
        {{-- ============================================================== --}}
        <section class="gold-card p-5 sm:p-6" wire:key="currency-rates">
            <div class="mb-5">
                <h2 class="text-lg sm:text-xl font-bold flex items-center gap-2" style="color: #0A2E23;">
                    <svg class="w-5 h-5" style="color: #C6963C;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Currency Exchange Rates (PKR)
                </h2>
                <div class="h-0.5 w-16 bg-gradient-to-r from-gold to-transparent mt-1 rounded-full"></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(198, 150, 60, 0.2);">
                            <th class="text-left py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #0A2E23;">Currency</th>
                            <th class="text-right py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #1B5E20;">Buy</th>
                            <th class="text-right py-3 px-3 font-semibold uppercase text-xs tracking-wider" style="color: #E65100;">Sell</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currencyMeta = [
                                'usd_interbank' => ['flag' => "\u{1F3E6}", 'name' => 'Interbank Dollar (USD)'],
                                'usd_pkr' => ['flag' => "\u{1F1FA}\u{1F1F8}", 'name' => 'Open Market Dollar (USD)'],
                                'gbp_pkr' => ['flag' => "\u{1F1EC}\u{1F1E7}", 'name' => 'British Pound (GBP)'],
                                'eur_pkr' => ['flag' => "\u{1F1EA}\u{1F1FA}", 'name' => 'Euro (EUR)'],
                                'myr_pkr' => ['flag' => "\u{1F1F2}\u{1F1FE}", 'name' => 'Malaysian Ringgit (MYR)'],
                                'sar_pkr' => ['flag' => "\u{1F1F8}\u{1F1E6}", 'name' => 'Saudi Riyal (SAR)'],
                                'aed_pkr' => ['flag' => "\u{1F1E6}\u{1F1EA}", 'name' => 'Dubai Dirham (AED)'],
                            ];
                        @endphp
                        @foreach($currencyMeta as $key => $meta)
                            @php
                                $rate = $currencyRates[$key] ?? null;
                                $buyRate = null;
                                $sellRate = null;
                                if (is_array($rate)) {
                                    $buyRate = $rate['buy'] ?? null;
                                    $sellRate = $rate['sell'] ?? null;
                                } elseif (is_numeric($rate)) {
                                    $buyRate = $rate;
                                    $sellRate = $rate;
                                }
                            @endphp
                            <tr class="border-b transition-colors" style="border-color: rgba(198, 150, 60, 0.12);" wire:key="currency-{{ $key }}">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-lg">{{ $meta['flag'] }}</span>
                                        <span class="font-semibold" style="color: #0A2E23;">{{ $meta['name'] }}</span>
                                    </div>
                                </td>
                                <td class="text-right py-3.5 px-3">
                                    @if($buyRate !== null && $buyRate > 0)
                                        <span class="price-badge-buy" data-price="{{ $buyRate }}" data-pkey="currency-{{ $key }}-buy">Rs {{ number_format($buyRate, 2) }}</span>
                                    @else
                                        <span style="color: #ccc;">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-right py-3.5 px-3">
                                    @if($sellRate !== null && $sellRate > 0)
                                        <span class="price-badge-sell" data-price="{{ $sellRate }}" data-pkey="currency-{{ $key }}-sell">Rs {{ number_format($sellRate, 2) }}</span>
                                    @else
                                        <span style="color: #ccc;">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($lastUpdated)
                <div class="mt-4 pt-3 flex items-center gap-1.5 text-xs" style="border-top: 1px solid rgba(198, 150, 60, 0.15); color: #999;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Updated {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }}
                </div>
            @endif
        </section>

    </div>

</div>
