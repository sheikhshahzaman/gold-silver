{{-- Desktop Navigation Bar --}}
<header class="sticky top-0 z-50 hidden md:block" style="background: linear-gradient(135deg, #F7EDD8 0%, #FAECD2 50%, #F2E4C8 100%); border-bottom: 2px solid #C6963C; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Left: Logo --}}
            <a href="/" class="flex items-center gap-2.5 group">
                @php
                    $siteLogo = \App\Models\Setting::get('site_logo');
                    $siteName = \App\Models\Setting::get('site_name', 'PakGold Rates');
                @endphp
                @if($siteLogo && Storage::disk('public')->exists($siteLogo))
                    <img src="{{ Storage::disk('public')->url($siteLogo) }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain">
                @else
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #0F3D2E, #0A2E23);">
                        <svg class="w-5 h-5 text-gold-light" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                @endif
                <span class="text-xl font-bold tracking-tight" style="color: #0A2E23;">
                    @if(str_contains($siteName, ' '))
                        {{ str($siteName)->before(' ') }} <span style="color: #A67922;">{{ str($siteName)->after(' ') }}</span>
                    @else
                        {{ $siteName }}
                    @endif
                </span>
            </a>

            {{-- Center: Navigation Links --}}
            <nav class="flex items-center gap-0.5">
                @php
                    $navItems = [
                        ['route' => 'home', 'href' => '/', 'label' => 'Home'],
                        ['route' => 'buy', 'href' => '/buy', 'label' => 'Buy'],
                        ['route' => 'sell', 'href' => '/sell', 'label' => 'Sell'],
                        ['route' => 'zakat', 'href' => '/zakat-calculator', 'label' => 'Zakat Calculator'],
                        ['route' => 'contact', 'href' => '/contact', 'label' => 'Contact'],
                    ];
                @endphp
                @foreach($navItems as $item)
                    <a href="{{ $item['href'] }}"
                       class="px-4 py-2 text-sm font-semibold transition-all duration-200 rounded-md
                              {{ request()->routeIs($item['route'])
                                  ? 'text-white rounded-lg'
                                  : 'hover:bg-white/50' }}"
                       @if(request()->routeIs($item['route'])) style="background: linear-gradient(135deg, #0F3D2E, #165A42);" @endif
                       @if(!request()->routeIs($item['route'])) style="color: #1a1a1a;" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Right: Live Indicator --}}
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full" style="background: #0F3D2E;">
                <span class="live-dot"></span>
                <span class="text-xs font-bold text-green-400">Live</span>
            </div>

        </div>
    </div>
</header>
