<x-layouts.app title="More - Islamabad Bullion Exchange">
    <div class="max-w-2xl mx-auto px-4 py-6">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold mb-1" style="color: #0A2E23;">More</h1>
            <p class="text-sm" style="color: #888;">Tools & legal information</p>
        </div>

        {{-- Menu Cards --}}
        <div class="space-y-3">
            @php
                $menuItems = [
                    ['href' => '/zakat-calculator', 'title' => 'Calculate Zakat', 'desc' => 'Estimate your zakat based on current rates', 'icon' => 'M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z'],
                    ['href' => '/about-us', 'title' => 'About Us', 'desc' => 'Learn more about our company', 'icon' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'],
                    ['href' => '/disclaimer', 'title' => 'Disclaimer', 'desc' => 'Important notes about rates and information', 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'],
                    ['href' => '/privacy-policy', 'title' => 'Privacy Policy', 'desc' => 'How we handle your data', 'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
                    ['href' => '/terms-and-conditions', 'title' => 'Terms & Conditions', 'desc' => 'Read our terms of use', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                ];
            @endphp

            @foreach($menuItems as $item)
                <a href="{{ $item['href'] }}" class="glass-card p-4 flex items-center gap-4 transition-all duration-200 group block">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(198,150,60,0.15);">
                        <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-sm group-hover:text-gold transition-colors" style="color: #0A2E23;">{{ $item['title'] }}</h3>
                        <p class="text-xs mt-0.5" style="color: #999;">{{ $item['desc'] }}</p>
                    </div>
                    <svg class="w-5 h-5 group-hover:text-gold transition-colors flex-shrink-0" style="color: #ccc;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
