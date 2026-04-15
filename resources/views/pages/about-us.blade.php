<x-layouts.app title="About Us - Islamabad Bullion Exchange">

    {{-- Hero Section --}}
    <section class="relative overflow-hidden" style="background: linear-gradient(135deg, #0A2E23 0%, #143D2B 50%, #0A2E23 100%);">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 rounded-full" style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%);"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 rounded-full" style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%);"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <p class="text-xs tracking-[0.3em] uppercase mb-4" style="color: #E8C96A;">Established Since 2015</p>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">About <span style="color: #E8C96A;">Islamabad Bullion Exchange</span></h1>
                <p class="text-lg text-white/70 leading-relaxed">Pakistan's most trusted name in gold and silver trading. Authentic products, transparent pricing, and exceptional service.</p>
            </div>
        </div>
    </section>

    {{-- Mission & Vision --}}
    <section class="py-16 md:py-24" style="background: #0F2419;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12">
                {{-- Mission --}}
                <div class="rounded-2xl p-8 md:p-10 border" style="background: rgba(201,168,76,0.05); border-color: rgba(201,168,76,0.15);">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-6" style="background: rgba(201,168,76,0.15);">
                        <svg class="w-7 h-7" style="color: #E8C96A;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4">Our Mission</h2>
                    <p class="text-white/60 leading-relaxed">To empower buyers, sellers, and investors with transparent and reliable gold and silver rate information. We strive to set the standard for trust and quality in Pakistan's bullion market, making precious metal trading accessible to everyone.</p>
                </div>

                {{-- Vision --}}
                <div class="rounded-2xl p-8 md:p-10 border" style="background: rgba(201,168,76,0.05); border-color: rgba(201,168,76,0.15);">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-6" style="background: rgba(201,168,76,0.15);">
                        <svg class="w-7 h-7" style="color: #E8C96A;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4">Our Vision</h2>
                    <p class="text-white/60 leading-relaxed">To become Pakistan's leading bullion exchange platform — where every customer can buy and sell precious metals with complete confidence. We envision a future where gold and silver trading is fully transparent, secure, and backed by technology.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- What We Offer --}}
    <section class="py-16 md:py-24" style="background: #0A2E23;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="text-xs tracking-[0.3em] uppercase mb-3" style="color: #E8C96A;">Our Services</p>
                <h2 class="text-3xl md:text-4xl font-bold text-white">What We Offer</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $services = [
                        ['icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z', 'title' => 'Live Market Rates', 'desc' => 'Real-time gold and silver prices in Pakistani Rupees, updated every minute from multiple trusted sources.'],
                        ['icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z', 'title' => 'Buy Gold & Silver', 'desc' => 'Purchase 24K, 22K, 21K, and 18K gold bars, coins, and silver in Tola, grams, and ounce units.'],
                        ['icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z', 'title' => 'Sell Your Gold', 'desc' => 'Get the best rates when selling your gold and silver. Transparent pricing with no hidden charges.'],
                        ['icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', 'title' => 'QR Verified Products', 'desc' => 'Every piece comes with a QR code sticker. Scan to verify authenticity, weight, and purity instantly.'],
                        ['icon' => 'M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z', 'title' => 'Zakat Calculator', 'desc' => 'Calculate your Zakat obligation on gold and silver holdings using current market values.'],
                        ['icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Price History & Trends', 'desc' => 'Track historical gold and silver prices. Make informed decisions with our comprehensive market data.'],
                    ];
                @endphp

                @foreach($services as $service)
                <div class="rounded-2xl p-6 border transition-all duration-300 hover:-translate-y-1" style="background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.08);">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(201,168,76,0.12);">
                        <svg class="w-6 h-6" style="color: #E8C96A;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $service['icon'] }}" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ $service['title'] }}</h3>
                    <p class="text-sm text-white/50 leading-relaxed">{{ $service['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="py-16 md:py-24" style="background: #0F2419;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <p class="text-xs tracking-[0.3em] uppercase mb-3" style="color: #E8C96A;">Why Choose Us</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Trusted by Thousands Across Pakistan</h2>
                    <p class="text-white/60 leading-relaxed mb-8">Since 2015, we have built our reputation on integrity, transparency, and customer satisfaction. Our commitment to quality assurance and fair pricing has made us the preferred choice for gold and silver trading in Islamabad and beyond.</p>

                    <div class="space-y-4">
                        @php
                            $reasons = [
                                'Certified purity testing on every piece',
                                'Live, transparent pricing with no hidden fees',
                                'QR code authentication for every product',
                                'Experienced team with decades of expertise',
                                'Secure transactions and customer data protection',
                            ];
                        @endphp
                        @foreach($reasons as $reason)
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background: rgba(201,168,76,0.2);">
                                <svg class="w-3 h-3" style="color: #E8C96A;" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <span class="text-white/70 text-sm">{{ $reason }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-4">
                    @php
                        $stats = [
                            ['num' => '10+', 'label' => 'Years of Experience'],
                            ['num' => '5000+', 'label' => 'Happy Customers'],
                            ['num' => '99.9%', 'label' => 'Purity Guarantee'],
                            ['num' => '24/7', 'label' => 'Live Price Updates'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                    <div class="rounded-2xl p-6 text-center border" style="background: rgba(201,168,76,0.05); border-color: rgba(201,168,76,0.15);">
                        <p class="text-3xl md:text-4xl font-bold mb-2" style="color: #E8C96A;">{{ $stat['num'] }}</p>
                        <p class="text-xs text-white/50">{{ $stat['label'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 md:py-20" style="background: linear-gradient(135deg, #0A2E23, #143D2B);">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Start Trading?</h2>
            <p class="text-white/60 mb-8">Whether you're buying your first gold bar or selling your collection, we're here to help with trusted service and the best rates.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/buy" class="px-8 py-3 rounded-lg text-sm font-semibold transition-all hover:opacity-90" style="background: #C9A84C; color: #0F2419;">Buy Gold & Silver</a>
                <a href="/contact" class="px-8 py-3 rounded-lg text-sm font-semibold transition-all border hover:bg-white/5" style="border-color: rgba(201,168,76,0.4); color: #E8C96A;">Contact Us</a>
            </div>
        </div>
    </section>

</x-layouts.app>
