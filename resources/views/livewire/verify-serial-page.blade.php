<div class="min-h-screen relative overflow-hidden" style="background: radial-gradient(ellipse at top, #0A2E23 0%, #060F09 70%);">

    {{-- Ambient background glow --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 right-1/4 w-96 h-96 rounded-full blur-3xl opacity-15" style="background: #C9A84C;"></div>
        <div class="absolute bottom-20 left-1/4 w-96 h-96 rounded-full blur-3xl opacity-10" style="background: #E8C96A;"></div>
        {{-- Floating particles --}}
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="particle particle-4"></div>
        <div class="particle particle-5"></div>
    </div>

    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">

        @if(!$submitted)
            {{-- ENTRY FORM --}}
            <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 50)">

                {{-- Header --}}
                <div class="text-center mb-10" :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'" style="transition: all 0.6s ease-out;">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-4" style="background: rgba(201,168,76,0.1); border: 1px solid rgba(201,168,76,0.3);">
                        <svg class="w-3.5 h-3.5" style="color: #E8C96A;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        <span class="text-[11px] tracking-widest font-medium uppercase" style="color: #E8C96A;">Authenticity Verification</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-bold text-white mb-3 tracking-tight">
                        Verify Your <span style="color: #E8C96A;">Serial Number</span>
                    </h1>
                    <p class="text-sm md:text-base max-w-xl mx-auto" style="color: rgba(255,255,255,0.6);">
                        Each genuine piece from Islamabad Bullion Exchange carries a unique serial number on its box. Enter it below to confirm it is authentic.
                    </p>
                </div>

                {{-- Form card with animated gold border --}}
                <div class="relative rounded-3xl p-[1.5px] mb-6 entry-glow" :class="loaded ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" style="transition: all 0.7s ease-out 0.1s;">
                    <div class="rounded-[22px] p-6 md:p-10" style="background: linear-gradient(135deg, #0F2419 0%, #0A2E23 100%);">

                        <form wire:submit="verify" class="space-y-6">

                            {{-- Serial Number Input --}}
                            <div>
                                <label class="block text-[11px] tracking-widest font-medium uppercase mb-3" style="color: #E8C96A;">
                                    Serial Number <span class="text-red-400">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 transition-colors group-focus-within:scale-110" style="color: #C9A84C;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        wire:model="serialNumber"
                                        placeholder="IBE-G24K-000001"
                                        autocomplete="off"
                                        autocapitalize="characters"
                                        class="w-full pl-12 pr-4 py-4 md:py-5 rounded-2xl text-lg md:text-xl font-mono tracking-wider text-white placeholder:text-white/30 border-2 transition-all focus:outline-none"
                                        style="background: rgba(0,0,0,0.3); border-color: rgba(201,168,76,0.25); letter-spacing: 0.1em;"
                                        onfocus="this.style.borderColor='#E8C96A'; this.style.boxShadow='0 0 0 4px rgba(232,201,106,0.1)'"
                                        onblur="this.style.borderColor='rgba(201,168,76,0.25)'; this.style.boxShadow='none'"
                                        required
                                    >
                                </div>
                                @error('serialNumber') <p class="mt-2 text-xs text-red-400">{{ $message }}</p> @enderror
                                <p class="mt-2 text-xs" style="color: rgba(255,255,255,0.4);">Format: <span class="font-mono" style="color: #E8C96A;">IBE-G24K-000001</span> (printed on your box)</p>
                            </div>

                            {{-- Optional contact fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] tracking-widest font-medium uppercase mb-3" style="color: rgba(255,255,255,0.5);">
                                        Your Name <span class="normal-case font-normal text-white/40">(optional)</span>
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="customerName"
                                        placeholder="e.g. Ahmed Khan"
                                        class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder:text-white/30 border transition-all focus:outline-none focus:border-amber-400"
                                        style="background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.1);"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] tracking-widest font-medium uppercase mb-3" style="color: rgba(255,255,255,0.5);">
                                        Your Phone <span class="normal-case font-normal text-white/40">(optional)</span>
                                    </label>
                                    <input
                                        type="tel"
                                        wire:model="customerPhone"
                                        placeholder="03XX-XXXXXXX"
                                        class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder:text-white/30 border transition-all focus:outline-none focus:border-amber-400"
                                        style="background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.1);"
                                    >
                                </div>
                            </div>

                            {{-- Submit --}}
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="verify"
                                class="w-full py-4 rounded-2xl text-sm md:text-base font-bold tracking-wide transition-all hover:scale-[1.02] active:scale-[0.99] disabled:opacity-50 disabled:hover:scale-100"
                                style="background: linear-gradient(135deg, #C9A84C 0%, #E8C96A 50%, #C9A84C 100%); background-size: 200% auto; color: #0F2419; box-shadow: 0 8px 30px rgba(201,168,76,0.3);"
                                onmouseover="this.style.backgroundPosition='right center'"
                                onmouseout="this.style.backgroundPosition='left center'"
                            >
                                <span wire:loading.remove wire:target="verify" class="inline-flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                    Verify Authenticity
                                </span>
                                <span wire:loading wire:target="verify" class="inline-flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                    Verifying...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Help row --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-8" :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition: all 0.7s ease-out 0.2s;">
                    <div class="rounded-2xl p-4 transition-transform hover:-translate-y-1" style="background: rgba(10,46,35,0.5); border: 1px solid rgba(201,168,76,0.12);">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(201,168,76,0.15);">
                                <svg class="w-4 h-4" style="color: #E8C96A;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-white mb-1">Where to find it</h3>
                                <p class="text-[11px] leading-relaxed" style="color: rgba(255,255,255,0.5);">On the sticker or card inside your sealed box.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl p-4 transition-transform hover:-translate-y-1" style="background: rgba(10,46,35,0.5); border: 1px solid rgba(201,168,76,0.12);">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(201,168,76,0.15);">
                                <svg class="w-4 h-4" style="color: #E8C96A;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 14.25h1.5v1.5h-1.5v-1.5zM16.5 14.25h1.5v1.5h-1.5v-1.5zM19.5 14.25h.75v1.5h-.75v-1.5zM13.5 17.25h1.5v1.5h-1.5v-1.5zM13.5 20.25h1.5v.75h-1.5v-.75zM16.5 17.25h3v3h-3v-3z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-white mb-1">Prefer scanning?</h3>
                                <p class="text-[11px] leading-relaxed" style="color: rgba(255,255,255,0.5);"><a href="/scan" class="underline hover:text-amber-200" style="color: #E8C96A;">Scan the QR code</a> with your camera.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl p-4 transition-transform hover:-translate-y-1" style="background: rgba(10,46,35,0.5); border: 1px solid rgba(201,168,76,0.12);">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(201,168,76,0.15);">
                                <svg class="w-4 h-4" style="color: #E8C96A;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-white mb-1">Need help?</h3>
                                <p class="text-[11px] leading-relaxed" style="color: rgba(255,255,255,0.5);"><a href="/contact" class="underline hover:text-amber-200" style="color: #E8C96A;">Contact our team</a> — we respond within an hour.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($isValid && ($foundItem || $foundSerial))
            @php
                // Normalize fields from either source
                $serialText    = $foundItem?->serial_number ?? $foundSerial?->serial_number;
                $productName   = $foundItem?->product?->name ?? $foundSerial?->product_name;
                $metal         = $foundItem?->product?->metal ?? $foundSerial?->metal;
                $karat         = $foundItem?->product?->karat ?? $foundSerial?->karat;
                $weight        = $foundItem?->actual_weight ?? $foundSerial?->weight;
                $purity        = $foundItem?->purity_tested ?? $foundSerial?->purity;
                $category      = $foundItem?->product?->productCategory?->name;
                $statusKey     = $foundItem?->status ?? 'approved';
                $soldAt        = $foundItem?->sold_at;
                $detailUrl     = $foundItem ? route('verify', ['token' => $foundItem->verification_token]) : null;
            @endphp
            {{-- SUCCESS STATE --}}
            <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 50)" class="text-center">

                {{-- Animated success ring --}}
                <div class="relative w-32 h-32 md:w-40 md:h-40 mx-auto mb-6" :class="loaded ? 'opacity-100 scale-100' : 'opacity-0 scale-50'" style="transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);">
                    {{-- Concentric rings --}}
                    <div class="absolute inset-0 rounded-full success-ring-1" style="background: radial-gradient(circle, rgba(16,185,129,0.4) 0%, transparent 70%);"></div>
                    <div class="absolute inset-2 rounded-full success-ring-2" style="background: radial-gradient(circle, rgba(16,185,129,0.3) 0%, transparent 70%);"></div>
                    <div class="absolute inset-4 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 0 40px rgba(16,185,129,0.5);">
                        <svg class="w-16 h-16 md:w-20 md:h-20 text-white success-check" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                </div>

                <div :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition: all 0.6s ease-out 0.3s;">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-4" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.35);">
                        <span class="text-[11px] tracking-widest font-medium uppercase text-emerald-300">100% Authentic</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-bold text-white mb-3 tracking-tight">
                        Verified <span style="color: #E8C96A;">Genuine</span>
                    </h1>
                    <p class="text-sm md:text-base max-w-xl mx-auto mb-8" style="color: rgba(255,255,255,0.65);">
                        This piece is an authentic product from Islamabad Bullion Exchange. Serial number <span class="font-mono font-semibold" style="color: #E8C96A;">{{ $serialText }}</span> matches our records.
                    </p>
                </div>

                {{-- Product Detail Card --}}
                <div class="relative rounded-3xl p-[1.5px] max-w-xl mx-auto mb-6" :class="loaded ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" style="background: linear-gradient(135deg, rgba(16,185,129,0.5), rgba(232,201,106,0.3)); transition: all 0.7s ease-out 0.4s;">
                    <div class="rounded-[22px] p-6 md:p-8 text-left" style="background: linear-gradient(135deg, #0F2419 0%, #0A2E23 100%);">

                        {{-- Status badge --}}
                        @php
                            $statusLabels = [
                                'in_stock' => ['In Stock', 'rgba(16,185,129,0.15)', 'rgba(16,185,129,0.4)', '#6ee7b7'],
                                'reserved' => ['Reserved', 'rgba(59,130,246,0.15)', 'rgba(59,130,246,0.4)', '#93c5fd'],
                                'sold' => ['Sold', 'rgba(201,168,76,0.15)', 'rgba(201,168,76,0.4)', '#E8C96A'],
                                'returned' => ['Returned', 'rgba(239,68,68,0.15)', 'rgba(239,68,68,0.4)', '#fca5a5'],
                                'approved' => ['Approved', 'rgba(16,185,129,0.15)', 'rgba(16,185,129,0.4)', '#6ee7b7'],
                            ];
                            [$statusLabel, $bg, $border, $color] = $statusLabels[$statusKey] ?? ['Unknown', 'rgba(255,255,255,0.1)', 'rgba(255,255,255,0.2)', '#fff'];
                        @endphp
                        <div class="flex items-center justify-between mb-6 pb-6 border-b" style="border-color: rgba(255,255,255,0.08);">
                            <div>
                                <p class="text-[10px] tracking-widest font-medium uppercase mb-1" style="color: rgba(255,255,255,0.4);">Current Status</p>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $bg }}; border: 1px solid {{ $border }}; color: {{ $color }};">
                                    {{ $statusLabel }}
                                    @if($statusKey === 'sold' && $soldAt)
                                        on {{ $soldAt->format('M j, Y') }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] tracking-widest font-medium uppercase mb-1" style="color: rgba(255,255,255,0.4);">Serial</p>
                                <p class="font-mono text-sm font-semibold" style="color: #E8C96A;">{{ $serialText }}</p>
                            </div>
                        </div>

                        {{-- Product details --}}
                        <div class="space-y-3">
                            @if($productName)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">Product</span>
                                    <span class="text-sm font-medium text-white">{{ $productName }}</span>
                                </div>
                            @endif
                            @if($metal)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">Metal</span>
                                    <span class="text-sm font-medium text-white">{{ ucfirst($metal) }} {{ $karat }}</span>
                                </div>
                            @endif
                            @if($weight)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">Weight</span>
                                    <span class="text-sm font-medium text-white">{{ $weight }}g</span>
                                </div>
                            @endif
                            @if($purity)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">Purity</span>
                                    <span class="text-sm font-medium text-white">{{ $purity }}</span>
                                </div>
                            @endif
                            @if($category)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">Category</span>
                                    <span class="text-sm font-medium text-white">{{ $category }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center" :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition: all 0.7s ease-out 0.5s;">
                    @if($detailUrl)
                        <a href="{{ $detailUrl }}" class="w-full sm:w-auto px-6 py-3 rounded-full text-sm font-semibold transition-all hover:scale-105 inline-flex items-center justify-center gap-2" style="background: linear-gradient(135deg, #C9A84C, #E8C96A); color: #0F2419;">
                            View Full Details
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('contact') }}" class="w-full sm:w-auto px-6 py-3 rounded-full text-sm font-semibold transition-all hover:scale-105 inline-flex items-center justify-center gap-2" style="background: linear-gradient(135deg, #C9A84C, #E8C96A); color: #0F2419;">
                            Contact Us
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                        </a>
                    @endif
                    <button wire:click="reset_form" class="w-full sm:w-auto px-6 py-3 rounded-full text-sm font-semibold transition-colors" style="background: transparent; border: 1.5px solid rgba(201,168,76,0.4); color: #E8C96A;">
                        Verify Another Piece
                    </button>
                </div>
            </div>

        @else
            {{-- FAILURE STATE --}}
            <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 50)" class="text-center">

                <div class="relative w-32 h-32 md:w-40 md:h-40 mx-auto mb-6" :class="loaded ? 'opacity-100 scale-100' : 'opacity-0 scale-50'" style="transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);">
                    <div class="absolute inset-0 rounded-full failure-ring" style="background: radial-gradient(circle, rgba(239,68,68,0.3) 0%, transparent 70%);"></div>
                    <div class="absolute inset-4 rounded-full flex items-center justify-center failure-shake" style="background: linear-gradient(135deg, #dc2626, #991b1b); box-shadow: 0 0 40px rgba(239,68,68,0.4);">
                        <svg class="w-16 h-16 md:w-20 md:h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                </div>

                <div :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition: all 0.6s ease-out 0.3s;">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-4" style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3);">
                        <span class="text-[11px] tracking-widest font-medium uppercase text-red-300">Not Recognized</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-bold text-white mb-3 tracking-tight">
                        Verification <span class="text-red-400">Failed</span>
                    </h1>
                    <p class="text-sm md:text-base max-w-xl mx-auto mb-8" style="color: rgba(255,255,255,0.65);">
                        {{ $errorMessage }}
                    </p>
                </div>

                <div class="max-w-xl mx-auto rounded-2xl p-5 md:p-6 mb-6 text-left" :class="loaded ? 'opacity-100 scale-100' : 'opacity-0 scale-95'" style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.25); transition: all 0.7s ease-out 0.4s;">
                    <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        What this means
                    </h3>
                    <ul class="space-y-2 text-xs" style="color: rgba(255,255,255,0.6);">
                        <li class="flex gap-2"><span class="text-red-400 shrink-0">•</span><span>The serial <span class="font-mono" style="color: #E8C96A;">{{ $serialNumber }}</span> is not in our records.</span></li>
                        <li class="flex gap-2"><span class="text-red-400 shrink-0">•</span><span>Double-check for typos — serials look like <span class="font-mono" style="color: #E8C96A;">IBE-G24K-000001</span>.</span></li>
                        <li class="flex gap-2"><span class="text-red-400 shrink-0">•</span><span>If the serial is correct, this piece was not supplied by Islamabad Bullion Exchange.</span></li>
                        <li class="flex gap-2"><span class="text-red-400 shrink-0">•</span><span>For confirmation, please contact our support team directly.</span></li>
                    </ul>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center" :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition: all 0.7s ease-out 0.5s;">
                    <button wire:click="reset_form" class="w-full sm:w-auto px-6 py-3 rounded-full text-sm font-semibold transition-all hover:scale-105" style="background: linear-gradient(135deg, #C9A84C, #E8C96A); color: #0F2419;">
                        Try Another Serial
                    </button>
                    <a href="{{ route('contact') }}" class="w-full sm:w-auto px-6 py-3 rounded-full text-sm font-semibold transition-colors inline-flex items-center justify-center gap-2" style="background: transparent; border: 1.5px solid rgba(201,168,76,0.4); color: #E8C96A;">
                        Contact Support
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <style>
        @keyframes entry-glow {
            0%, 100% { background: linear-gradient(135deg, rgba(201,168,76,0.6), rgba(232,201,106,0.15) 40%, rgba(201,168,76,0.3) 80%, rgba(232,201,106,0.55)); }
            50%      { background: linear-gradient(135deg, rgba(232,201,106,0.7), rgba(201,168,76,0.2) 40%, rgba(232,201,106,0.4) 80%, rgba(201,168,76,0.7)); }
        }
        .entry-glow { animation: entry-glow 4s ease-in-out infinite; }

        @keyframes success-ring-pulse {
            0%, 100% { transform: scale(1); opacity: 0.4; }
            50% { transform: scale(1.15); opacity: 0.8; }
        }
        .success-ring-1 { animation: success-ring-pulse 2s ease-in-out infinite; }
        .success-ring-2 { animation: success-ring-pulse 2s ease-in-out infinite 0.4s; }

        @keyframes success-check-draw {
            0% { transform: scale(0) rotate(-90deg); opacity: 0; }
            60% { transform: scale(1.2) rotate(0deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .success-check { animation: success-check-draw 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both; }

        @keyframes failure-ring-pulse {
            0%, 100% { transform: scale(1); opacity: 0.4; }
            50% { transform: scale(1.1); opacity: 0.7; }
        }
        .failure-ring { animation: failure-ring-pulse 2s ease-in-out infinite; }

        @keyframes failure-shake-anim {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-6px); }
            80% { transform: translateX(6px); }
        }
        .failure-shake { animation: failure-shake-anim 0.6s ease-in-out 0.3s; }

        @keyframes float-particle {
            0%, 100% { transform: translate(0, 0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            50% { transform: translate(20px, -30px); opacity: 0.8; }
        }
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #E8C96A;
            box-shadow: 0 0 8px #E8C96A;
            opacity: 0.3;
        }
        .particle-1 { top: 15%; left: 10%; animation: float-particle 6s ease-in-out infinite; }
        .particle-2 { top: 30%; right: 15%; animation: float-particle 7s ease-in-out infinite 1s; }
        .particle-3 { bottom: 25%; left: 20%; animation: float-particle 5s ease-in-out infinite 2s; }
        .particle-4 { top: 60%; right: 25%; animation: float-particle 8s ease-in-out infinite 0.5s; }
        .particle-5 { bottom: 40%; right: 10%; animation: float-particle 6.5s ease-in-out infinite 1.5s; }
    </style>
</div>
