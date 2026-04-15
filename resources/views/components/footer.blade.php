{{-- Site Footer --}}
<footer style="background: #060F09; color: rgba(255,255,255,0.5);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">

            {{-- Column 1: Brand --}}
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    @php
                        $footerLogo = \App\Models\Setting::get('site_logo');
                        $footerSiteName = \App\Models\Setting::get('site_name', 'Islamabad Bullion Exchange');
                    @endphp
                    @if($footerLogo && Storage::disk('public')->exists($footerLogo))
                        <img src="{{ Storage::disk('public')->url($footerLogo) }}" alt="{{ $footerSiteName }}" class="h-8 w-auto object-contain">
                    @else
                        <div class="w-8 h-8 rounded flex items-center justify-center" style="background: #C9A84C;">
                            <svg class="w-4 h-4 text-emerald-950" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                            </svg>
                        </div>
                    @endif
                    <span class="text-sm text-white">
                        @if(str_contains($footerSiteName, ' '))
                            {{ str($footerSiteName)->before(' ') }} <span style="color: #E8C96A; font-weight: 400;">{{ str($footerSiteName)->after(' ') }}</span>
                        @else
                            {{ $footerSiteName }}
                        @endif
                    </span>
                </div>
                <p class="text-xs leading-relaxed max-w-[220px]" style="color: rgba(255,255,255,0.4);">
                    Pakistan's trusted gold and silver exchange since 2015. Authentic products, live rates, and expert service.
                </p>
            </div>

            {{-- Column 2: Quick Links --}}
            <div>
                <h4 class="text-[11px] tracking-wider font-medium mb-4" style="color: #E8C96A;">QUICK LINKS</h4>
                <div class="space-y-2">
                    @foreach([
                        ['href' => '/', 'label' => 'Home'],
                        ['href' => '/buy', 'label' => 'Buy Gold & Silver'],
                        ['href' => '/sell', 'label' => 'Sell Gold & Silver'],
                        ['href' => '/products', 'label' => 'Products'],
                        ['href' => '/zakat-calculator', 'label' => 'Zakat Calculator'],
                        ['href' => '/contact', 'label' => 'Contact Us'],
                    ] as $link)
                        <a href="{{ $link['href'] }}" class="block text-xs transition-colors hover:text-gold-light" style="color: rgba(255,255,255,0.4);">{{ $link['label'] }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Column 3: Legal --}}
            <div>
                <h4 class="text-[11px] tracking-wider font-medium mb-4" style="color: #E8C96A;">LEGAL</h4>
                <div class="space-y-2">
                    @foreach([
                        ['href' => '/about-us', 'label' => 'About Us'],
                        ['href' => '/disclaimer', 'label' => 'Disclaimer'],
                        ['href' => '/privacy-policy', 'label' => 'Privacy Policy'],
                        ['href' => '/terms-and-conditions', 'label' => 'Terms & Conditions'],
                    ] as $link)
                        <a href="{{ $link['href'] }}" class="block text-xs transition-colors hover:text-gold-light" style="color: rgba(255,255,255,0.4);">{{ $link['label'] }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Column 4: Contact --}}
            <div>
                <h4 class="text-[11px] tracking-wider font-medium mb-4" style="color: #E8C96A;">CONTACT US</h4>
                <ul class="space-y-2 text-xs" style="color: rgba(255,255,255,0.4);">
                    <li class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" style="color: rgba(255,255,255,0.3);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <span>+92 300 0000000</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" style="color: rgba(255,255,255,0.3);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span>info@pakgold.com</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" style="color: rgba(255,255,255,0.3);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span>Islamabad, Pakistan</span>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Bottom Bar --}}
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <p class="text-[11px]" style="color: rgba(255,255,255,0.3);">
                &copy; {{ date('Y') }} Islamabad Bullion Exchange. All rights reserved.
            </p>
            <div class="flex gap-4">
                <a href="/privacy-policy" class="text-[11px] transition-colors hover:text-gold-light" style="color: rgba(255,255,255,0.3);">Privacy Policy</a>
                <a href="/terms-and-conditions" class="text-[11px] transition-colors hover:text-gold-light" style="color: rgba(255,255,255,0.3);">Terms of Service</a>
                <a href="/disclaimer" class="text-[11px] transition-colors hover:text-gold-light" style="color: rgba(255,255,255,0.3);">Disclaimer</a>
            </div>
        </div>
    </div>
</footer>
