{{-- Desktop Navigation Bar --}}
<header class="sticky top-0 z-50 hidden md:block" style="background: #0F2419;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Left: Logo --}}
            <a href="/" class="flex items-center gap-2.5 group">
                @php
                    $siteLogo = \App\Models\Setting::get('site_logo');
                    $siteName = \App\Models\Setting::get('site_name', 'Islamabad Bullion Exchange');
                @endphp
                @if($siteLogo && Storage::disk('public')->exists($siteLogo))
                    <img src="{{ Storage::disk('public')->url($siteLogo) }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain">
                @else
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: #C9A84C;">
                        <svg class="w-5 h-5 text-emerald-950" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                @endif
                <span class="text-base font-medium text-white">
                    @if(str_contains($siteName, ' '))
                        {{ str($siteName)->before(' ') }} <span style="color: #E8C96A;">{{ str($siteName)->after(' ') }}</span>
                    @else
                        {{ $siteName }}
                    @endif
                </span>
            </a>

            {{-- Center: Navigation Links --}}
            <nav class="flex items-center gap-6">
                @php
                    $navItems = [
                        ['route' => 'home', 'href' => '/', 'label' => 'Home'],
                        ['route' => 'buy', 'href' => '/buy', 'label' => 'Buy Gold'],
                        ['route' => 'sell', 'href' => '/sell', 'label' => 'Sell Gold'],
                        ['route' => 'page.about-us', 'href' => '/about-us', 'label' => 'About Us'],
                        ['route' => 'contact', 'href' => '/contact', 'label' => 'Contact'],
                    ];
                @endphp
                @foreach($navItems as $item)
                    <a href="{{ $item['href'] }}"
                       class="text-[13px] tracking-wide transition-colors
                              {{ request()->routeIs($item['route'])
                                  ? 'text-gold-light font-medium'
                                  : 'text-white/80 hover:text-gold-light' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Right: Scan QR + Live Badge + CTA --}}
            <div class="flex items-center gap-3">
                <a href="/scan" title="Scan QR Code" class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-medium transition-all hover:scale-105 group" style="background: rgba(201,168,76,0.1); border: 1px solid #C9A84C; color: #E8C96A;">
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 14.25h1.5v1.5h-1.5v-1.5zM16.5 14.25h1.5v1.5h-1.5v-1.5zM19.5 14.25h.75v1.5h-.75v-1.5zM13.5 17.25h1.5v1.5h-1.5v-1.5zM13.5 20.25h1.5v.75h-1.5v-.75zM16.5 17.25h3v3h-3v-3z"/>
                    </svg>
                    Scan QR
                </a>
                <a href="/live" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] transition-opacity hover:opacity-80" style="background: rgba(13,61,31,0.6); border: 1px solid #C9A84C; color: #E8C96A;">
                    <span class="live-dot"></span>
                    Live Rates
                </a>
                <a href="/buy" class="px-4 py-2 rounded text-xs font-medium" style="background: #C9A84C; color: #0F2419;">Get Quote</a>
            </div>

        </div>
    </div>
</header>
