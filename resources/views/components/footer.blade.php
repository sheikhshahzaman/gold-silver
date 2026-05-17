{{-- Editorial site footer --}}
@php
    $footerLogo = \App\Models\Setting::get('site_logo');
    $footerSiteName = \App\Models\Setting::get('site_name', 'Islamabad Bullion Exchange');
@endphp
<footer class="relative overflow-hidden" style="background: var(--espresso); color: var(--ivory);">

    {{-- Big faint serif watermark, mirrors the hero's "IBE" mark --}}
    <div aria-hidden="true" class="absolute -bottom-32 left-1/2 -translate-x-1/2 pointer-events-none select-none whitespace-nowrap" style="z-index: 0;">
        <span class="font-display" style="font-size: clamp(220px, 30vw, 460px); color: rgba(232, 201, 106, 0.04); letter-spacing: -0.04em; line-height: 0.85;">
            {{ str($footerSiteName)->before(' ') }}
        </span>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 sm:px-10 pt-24 sm:pt-32 pb-12" style="z-index: 1;">

        {{-- Top: brand statement + four columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 mb-20">

            {{-- Brand block (left) --}}
            <div class="lg:col-span-5">
                <div class="editorial-eyebrow no-rules mb-6" style="color: var(--gold-300);">
                    <span class="w-12 h-px" style="background: var(--gold-300);"></span>
                    Vol. 09 · Est. 2015
                </div>

                <a href="/" class="font-display block leading-[0.95] tracking-[-0.02em]" style="font-size: clamp(48px, 6vw, 96px); color: var(--ivory);">
                    @if(str_contains($footerSiteName, ' '))
                        {{ str($footerSiteName)->before(' ') }}<br>
                        <em style="color: var(--gold-300);">{{ str($footerSiteName)->after(' ') }}</em>
                    @else
                        {{ $footerSiteName }}
                    @endif
                </a>

                <p class="font-display-italic mt-8 text-xl leading-relaxed" style="color: rgba(245,241,232,0.55); max-width: 380px;">
                    Pakistan's trusted gold and silver house — refined for the way the world buys bullion today.
                </p>

                <div class="mt-10">
                    <a href="/contact" data-magnetic="0.18"
                       class="inline-flex items-center gap-3 text-[10px] tracking-[0.3em] uppercase pb-2 transition-colors"
                       style="color: var(--gold-300); border-bottom: 1px solid rgba(232,201,106,0.3);"
                       onmouseover="this.style.color='var(--ivory)'; this.style.borderColor='var(--ivory)';"
                       onmouseout="this.style.color='var(--gold-300)'; this.style.borderColor='rgba(232,201,106,0.3)';">
                        Write to us
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-6-6m6 6l-6 6"/></svg>
                    </a>
                </div>
            </div>

            {{-- Columns (right) --}}
            <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-10">

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-[10px] tracking-[0.3em] uppercase mb-6" style="color: var(--gold-300);">Navigate</h4>
                    <ul class="space-y-3">
                        @foreach([
                            ['/', 'Home'],
                            ['/buy', 'Buy'],
                            ['/sell', 'Sell'],
                            ['/products', 'Collection'],
                            ['/live', 'Live Rates'],
                            ['/zakat-calculator', 'Zakat Tool'],
                        ] as [$href, $label])
                            <li>
                                <a href="{{ $href }}" wire:navigate
                                   class="font-display text-lg transition-colors"
                                   style="color: rgba(245,241,232,0.7);"
                                   onmouseover="this.style.color='var(--gold-300)';"
                                   onmouseout="this.style.color='rgba(245,241,232,0.7)';">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- House / Legal --}}
                <div>
                    <h4 class="text-[10px] tracking-[0.3em] uppercase mb-6" style="color: var(--gold-300);">The House</h4>
                    <ul class="space-y-3">
                        @foreach([
                            ['/about-us', 'About'],
                            ['/scan', 'Scan QR'],
                            ['/verify', 'Verify Serial'],
                            ['/contact', 'Contact'],
                            ['/privacy-policy', 'Privacy'],
                            ['/terms-and-conditions', 'Terms'],
                        ] as [$href, $label])
                            <li>
                                <a href="{{ $href }}" wire:navigate
                                   class="font-display text-lg transition-colors"
                                   style="color: rgba(245,241,232,0.7);"
                                   onmouseover="this.style.color='var(--gold-300)';"
                                   onmouseout="this.style.color='rgba(245,241,232,0.7)';">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="text-[10px] tracking-[0.3em] uppercase mb-6" style="color: var(--gold-300);">Visit</h4>
                    @php
                        $phone = \App\Models\Setting::get('contact_phone', '+92 300 0000000');
                        $email = \App\Models\Setting::get('contact_email', 'info@islamabadbullionexchange.com');
                        $address = \App\Models\Setting::get('contact_address', 'F-7 Markaz<br>Islamabad, Pakistan');
                    @endphp
                    <div class="space-y-5">
                        <div>
                            <div class="text-[9px] tracking-[0.3em] uppercase mb-1" style="color: rgba(232,201,106,0.5);">Phone</div>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="font-display text-lg" style="color: var(--ivory);">{{ $phone }}</a>
                        </div>
                        <div>
                            <div class="text-[9px] tracking-[0.3em] uppercase mb-1" style="color: rgba(232,201,106,0.5);">Mail</div>
                            <a href="mailto:{{ $email }}" class="font-display text-base" style="color: var(--ivory); word-break: break-all;">{{ $email }}</a>
                        </div>
                        <div>
                            <div class="text-[9px] tracking-[0.3em] uppercase mb-1" style="color: rgba(232,201,106,0.5);">Address</div>
                            <div class="font-display text-lg leading-snug" style="color: var(--ivory);">{!! $address !!}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Thin gold divider --}}
        <hr class="editorial-rule mb-8" style="background: linear-gradient(90deg, transparent, rgba(232,201,106,0.35), transparent);">

        {{-- Bottom strip: copyright + colophon --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-[10px] tracking-[0.25em] uppercase" style="color: rgba(245,241,232,0.4);">
            <div>
                © {{ date('Y') }} · {{ $footerSiteName }} · All rights reserved
            </div>
            <div class="flex items-center gap-6">
                <a href="/privacy-policy" wire:navigate class="transition-colors" onmouseover="this.style.color='var(--gold-300)';" onmouseout="this.style.color='';">Privacy</a>
                <span style="color: rgba(245,241,232,0.2);">·</span>
                <a href="/terms-and-conditions" wire:navigate class="transition-colors" onmouseover="this.style.color='var(--gold-300)';" onmouseout="this.style.color='';">Terms</a>
                <span style="color: rgba(245,241,232,0.2);">·</span>
                <a href="/disclaimer" wire:navigate class="transition-colors" onmouseover="this.style.color='var(--gold-300)';" onmouseout="this.style.color='';">Disclaimer</a>
            </div>
        </div>
    </div>
</footer>
