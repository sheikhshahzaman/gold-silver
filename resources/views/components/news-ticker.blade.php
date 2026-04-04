{{-- Scrolling News Ticker --}}
@php
    $tickers = [];

    // Try to fetch from NewsTicker model if it exists
    try {
        if (class_exists(\App\Models\NewsTicker::class)) {
            $tickers = cache()->remember('news_tickers', 300, function () {
                return \App\Models\NewsTicker::where('is_active', true)
                    ->orderBy('sort_order')
                    ->pluck('content')
                    ->toArray();
            });
        }
    } catch (\Exception $e) {
        // Model or table doesn't exist yet
    }

    if (empty($tickers)) {
        $tickers = [
            'Gold prices updated every 5 minutes throughout the trading day.',
            'PakGold Rates provides the most accurate bullion prices in Pakistan.',
            'Use our Zakat Calculator to determine your obligation on gold and silver holdings.',
            'Contact us for bulk buying and selling of gold and silver.',
        ];
    }

    $tickerText = implode('  ★  ', $tickers);
@endphp

<div class="py-1.5 overflow-hidden" style="background: #0F3D2E; border-bottom: 1px solid rgba(198, 150, 60, 0.2);">
    <div class="flex items-center">
        {{-- News Badge --}}
        <span class="shrink-0 ml-3 mr-3 px-2 py-0.5 bg-gold/20 border border-gold/30 rounded text-gold text-[10px] font-bold uppercase tracking-wider z-10">
            News
        </span>

        {{-- Scrolling Content --}}
        <div class="ticker-wrapper flex-1">
            <div class="ticker-content">
                <span class="text-gold/80 text-xs font-medium">
                    {{ $tickerText }}  ★  {{ $tickerText }}
                </span>
            </div>
        </div>
    </div>
</div>
