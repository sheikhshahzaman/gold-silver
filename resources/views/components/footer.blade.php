{{-- Site Footer --}}
<footer style="background: linear-gradient(180deg, #F7EDD8 0%, #F0E2C5 100%); border-top: 2px solid #C6963C;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Column 1: Brand --}}
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    @php
                        $footerLogo = \App\Models\Setting::get('site_logo');
                        $footerSiteName = \App\Models\Setting::get('site_name', 'PakGold Rates');
                    @endphp
                    @if($footerLogo && Storage::disk('public')->exists($footerLogo))
                        <img src="{{ Storage::disk('public')->url($footerLogo) }}" alt="{{ $footerSiteName }}" class="h-9 w-auto object-contain">
                    @else
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #0F3D2E, #0A2E23);">
                            <svg class="w-5 h-5 text-gold-light" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                            </svg>
                        </div>
                    @endif
                    <span class="text-xl font-bold" style="color: #0A2E23;">
                        @if(str_contains($footerSiteName, ' '))
                            {{ str($footerSiteName)->before(' ') }} <span style="color: #A67922;">{{ str($footerSiteName)->after(' ') }}</span>
                        @else
                            {{ $footerSiteName }}
                        @endif
                    </span>
                </div>
                <p class="text-sm leading-relaxed" style="color: #555;">
                    Islamabad's trusted source for live gold, silver &amp; currency rates. Real-time bullion prices from Islamabad/Rawalpindi market benchmark.
                </p>
            </div>

            {{-- Column 2: Quick Links --}}
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wider mb-4" style="color: #0A2E23;">Quick Links</h4>
                <ul class="space-y-2.5">
                    @php
                        $quickLinks = [
                            ['href' => '/', 'label' => 'Home'],
                            ['href' => '/buy', 'label' => 'Buy Gold & Silver'],
                            ['href' => '/sell', 'label' => 'Sell Gold & Silver'],
                            ['href' => '/zakat-calculator', 'label' => 'Zakat Calculator'],
                            ['href' => '/contact', 'label' => 'Contact Us'],
                        ];
                    @endphp
                    @foreach($quickLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="text-sm transition-colors flex items-center gap-2 hover:underline" style="color: #0F3D2E;">
                                <span class="w-1.5 h-1.5 rounded-full" style="background: #C6963C;"></span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Column 3: Legal Pages --}}
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wider mb-4" style="color: #0A2E23;">Legal</h4>
                <ul class="space-y-2.5">
                    @php
                        $legalLinks = [
                            ['href' => '/about-us', 'label' => 'About Us'],
                            ['href' => '/disclaimer', 'label' => 'Disclaimer'],
                            ['href' => '/privacy-policy', 'label' => 'Privacy Policy'],
                            ['href' => '/terms-and-conditions', 'label' => 'Terms & Conditions'],
                        ];
                    @endphp
                    @foreach($legalLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="text-sm transition-colors flex items-center gap-2 hover:underline" style="color: #0F3D2E;">
                                <span class="w-1.5 h-1.5 rounded-full" style="background: #C6963C;"></span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Column 4: Contact Info --}}
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wider mb-4" style="color: #0A2E23;">Contact Us</h4>
                <ul class="space-y-3 text-sm" style="color: #444;">
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" style="color: #0F3D2E;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <span>+92 300 0000000</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" style="color: #0F3D2E;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span>info@pakgold.com</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" style="color: #0F3D2E;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span>Islamabad, Pakistan</span>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Bottom Bar --}}
        <div class="mt-10 pt-6" style="border-top: 1px solid #C6963C40;">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-xs" style="color: #888;">
                    &copy; {{ date('Y') }} Islamabad Bullion Exchange. All rights reserved.
                </p>
                <p class="text-xs text-center md:text-right max-w-xl" style="color: #aaa;">
                    Disclaimer: Rates displayed are for informational purposes only and may vary at the time of transaction. Always confirm with your dealer before trading.
                </p>
            </div>
        </div>
    </div>
</footer>
