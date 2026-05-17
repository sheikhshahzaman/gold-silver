{{-- Editorial Desktop Navigation Bar --}}
<header class="sticky top-0 z-50 hidden md:block" style="background: rgba(245, 241, 232, 0.85); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(139, 105, 20, 0.18);">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="flex items-center justify-between h-20">

            {{-- Left: Brand mark with serif wordmark --}}
            <a href="/" class="flex items-center gap-3 group">
                @php
                    $siteLogo = \App\Models\Setting::get('site_logo');
                    $siteName = \App\Models\Setting::get('site_name', 'Islamabad Bullion Exchange');
                @endphp
                @if($siteLogo && Storage::disk('public')->exists($siteLogo))
                    <img src="{{ Storage::disk('public')->url($siteLogo) }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-9 h-9 items-center justify-center" style="background: var(--ink); display: none;">
                        <span class="font-display text-base" style="color: var(--gold-300); letter-spacing: 0.02em;">A</span>
                    </div>
                @else
                    <div class="w-10 h-10 flex items-center justify-center" style="background: var(--ink);">
                        <span class="font-display text-lg" style="color: var(--gold-300); line-height: 1; letter-spacing: 0.02em;">A</span>
                    </div>
                @endif

                <div class="flex flex-col leading-tight">
                    @if(str_contains($siteName, ' '))
                        <span class="font-display text-lg" style="color: var(--ink); letter-spacing: -0.005em; line-height: 1;">
                            {{ str($siteName)->before(' ') }}
                            <em style="color: var(--gold-700);">{{ str($siteName)->after(' ') }}</em>
                        </span>
                    @else
                        <span class="font-display text-lg" style="color: var(--ink);">{{ $siteName }}</span>
                    @endif
                    <span class="text-[9px] tracking-[0.3em] uppercase mt-0.5" style="color: var(--gold-700);">Est. 2015 · Islamabad</span>
                </div>
            </a>

            {{-- Center: Navigation Links (editorial style — uppercase tracked) --}}
            <nav class="flex items-center gap-10">
                @php
                    $navItems = [
                        ['route' => 'home', 'href' => '/', 'label' => 'Home'],
                        ['route' => 'buy', 'href' => '/buy', 'label' => 'Buy Gold'],
                        ['route' => 'sell', 'href' => '/sell', 'label' => 'Sell Gold'],
                        ['route' => 'page.about-us', 'href' => '/about-us', 'label' => 'About'],
                        ['route' => 'contact', 'href' => '/contact', 'label' => 'Contact'],
                    ];
                @endphp
                @foreach($navItems as $item)
                    @php $isActive = request()->routeIs($item['route']); @endphp
                    <a href="{{ $item['href'] }}" wire:navigate data-magnetic="0.18"
                       class="relative text-[11px] tracking-[0.28em] uppercase transition-colors"
                       style="color: {{ $isActive ? 'var(--ink)' : 'rgba(26,20,16,0.55)' }};"
                       onmouseover="this.style.color='var(--ink)';"
                       onmouseout="this.style.color='{{ $isActive ? 'var(--ink)' : 'rgba(26,20,16,0.55)' }}';">
                        {{ $item['label'] }}
                        @if($isActive)
                            <span class="absolute -bottom-1.5 left-0 right-0 h-px" style="background: var(--gold-500);"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Right: Live rates pill + primary CTA --}}
            <div class="flex items-center gap-4">
                <a href="/scan" title="Scan QR Code"
                   class="flex items-center gap-2 text-[10px] tracking-[0.3em] uppercase transition-colors"
                   style="color: rgba(26,20,16,0.55);"
                   onmouseover="this.style.color='var(--gold-700)';"
                   onmouseout="this.style.color='rgba(26,20,16,0.55)';">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 14.25h1.5v1.5h-1.5v-1.5zM16.5 14.25h1.5v1.5h-1.5v-1.5zM19.5 14.25h.75v1.5h-.75v-1.5zM13.5 17.25h1.5v1.5h-1.5v-1.5zM13.5 20.25h1.5v.75h-1.5v-.75zM16.5 17.25h3v3h-3v-3z"/></svg>
                    Scan
                </a>

                <a href="/live" data-magnetic="0.18"
                   class="flex items-center gap-2 px-4 py-2 text-[10px] tracking-[0.28em] uppercase transition-colors"
                   style="color: var(--gold-700); border: 1px solid rgba(139,105,20,0.35);"
                   onmouseover="this.style.borderColor='var(--gold-700)';"
                   onmouseout="this.style.borderColor='rgba(139,105,20,0.35)';">
                    <span class="live-dot" style="width: 6px; height: 6px;"></span>
                    Live Rates
                </a>

                <a href="/buy" data-magnetic="0.18"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-[10px] tracking-[0.28em] uppercase transition-all"
                   style="background: var(--ink); color: var(--ivory);"
                   onmouseover="this.style.background='var(--gold-700)';"
                   onmouseout="this.style.background='var(--ink)';">
                    Get a Quote
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-6-6m6 6l-6 6"/></svg>
                </a>
            </div>

        </div>
    </div>
</header>
