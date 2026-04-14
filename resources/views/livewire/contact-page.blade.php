<div style="background-color: #FAF6EE; min-height: 100vh;">

    {{-- Hero Banner --}}
    <div style="background: linear-gradient(135deg, #0A2E23 0%, #134a38 50%, #0A2E23 100%); position: relative; overflow: hidden;">
        {{-- Decorative pattern overlay --}}
        <div style="position: absolute; inset: 0; opacity: 0.05;">
            <div style="position: absolute; top: -50%; right: -20%; width: 600px; height: 600px; border-radius: 50%; border: 1px solid #C9A84C;"></div>
            <div style="position: absolute; bottom: -60%; left: -10%; width: 500px; height: 500px; border-radius: 50%; border: 1px solid #C9A84C;"></div>
        </div>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20 text-center" style="position: relative; z-index: 1;">
            {{-- Gold accent line --}}
            <div class="mx-auto mb-5" style="width: 50px; height: 3px; background: linear-gradient(90deg, #C9A84C, #E8D48B, #C9A84C); border-radius: 2px;"></div>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-white mb-3">Get in Touch</h1>
            <p class="text-base md:text-lg" style="color: rgba(255,255,255,0.65); max-width: 520px; margin: 0 auto;">
                We are here to help. Reach out to us for inquiries, quotes, or any assistance you need.
            </p>
        </div>
        {{-- Bottom curve --}}
        <div style="position: absolute; bottom: -1px; left: 0; right: 0;">
            <svg viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display: block; width: 100%; height: 40px;">
                <path d="M0 40V20C360 0 720 0 1080 20C1260 30 1380 38 1440 40V40H0Z" fill="#FAF6EE"/>
            </svg>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

            {{-- ============================================= --}}
            {{-- LEFT COLUMN: Contact Info + Map               --}}
            {{-- ============================================= --}}
            <div class="space-y-6">

                {{-- Contact Info Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Address Card --}}
                    <div x-data="{ copied: false }" class="rounded-2xl p-5 transition-shadow duration-300 hover:shadow-lg"
                         style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(10,46,35,0.06), 0 4px 12px rgba(10,46,35,0.04);">
                        <div class="flex items-center justify-center w-11 h-11 rounded-full mb-4"
                             style="background: linear-gradient(135deg, rgba(201,168,76,0.15), rgba(201,168,76,0.08));">
                            <svg class="w-5 h-5" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #C9A84C;">Address</p>
                        <p class="text-sm leading-relaxed mb-4" style="color: #333;">{{ $contactAddress }}</p>
                        <div class="flex flex-wrap gap-2">
                            <button @click="navigator.clipboard.writeText('{{ $contactAddress }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200"
                                style="background: #F7F2EA; color: #6B6B6B; border: 1px solid #EDE6D8;"
                                :style="copied && { background: '#ecfdf5', color: '#059669', borderColor: '#a7f3d0' }">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                                <span x-show="!copied">Copy</span>
                                <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span x-show="copied" x-cloak>Copied!</span>
                            </button>
                            <a href="https://maps.google.com/?q={{ urlencode($contactAddress) }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 hover:shadow-sm"
                                style="background: #F7F2EA; color: #6B6B6B; border: 1px solid #EDE6D8;">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                Open Map
                            </a>
                        </div>
                    </div>

                    {{-- Phone Card --}}
                    <div x-data="{ copied: false }" class="rounded-2xl p-5 transition-shadow duration-300 hover:shadow-lg"
                         style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(10,46,35,0.06), 0 4px 12px rgba(10,46,35,0.04);">
                        <div class="flex items-center justify-center w-11 h-11 rounded-full mb-4"
                             style="background: linear-gradient(135deg, rgba(201,168,76,0.15), rgba(201,168,76,0.08));">
                            <svg class="w-5 h-5" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #C9A84C;">Phone</p>
                        <p class="text-sm leading-relaxed mb-4" style="color: #333;">{{ $contactPhone }}</p>
                        <div class="flex flex-wrap gap-2">
                            <button @click="navigator.clipboard.writeText('{{ $contactPhone }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200"
                                style="background: #F7F2EA; color: #6B6B6B; border: 1px solid #EDE6D8;"
                                :style="copied && { background: '#ecfdf5', color: '#059669', borderColor: '#a7f3d0' }">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                                <span x-show="!copied">Copy</span>
                                <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span x-show="copied" x-cloak>Copied!</span>
                            </button>
                            <a href="tel:{{ $contactPhone }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 hover:shadow-sm"
                                style="background: #F7F2EA; color: #6B6B6B; border: 1px solid #EDE6D8;">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                                Call Now
                            </a>
                        </div>
                    </div>

                    {{-- WhatsApp Card --}}
                    <div x-data="{ copied: false }" class="rounded-2xl p-5 transition-shadow duration-300 hover:shadow-lg"
                         style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(10,46,35,0.06), 0 4px 12px rgba(10,46,35,0.04);">
                        <div class="flex items-center justify-center w-11 h-11 rounded-full mb-4"
                             style="background: linear-gradient(135deg, rgba(37,211,102,0.15), rgba(37,211,102,0.06));">
                            <svg class="w-5 h-5" style="color: #25D366;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #25D366;">WhatsApp</p>
                        <p class="text-sm leading-relaxed mb-4" style="color: #333;">{{ $contactWhatsapp }}</p>
                        <div class="flex flex-wrap gap-2">
                            <button @click="navigator.clipboard.writeText('{{ $contactWhatsapp }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200"
                                style="background: #F7F2EA; color: #6B6B6B; border: 1px solid #EDE6D8;"
                                :style="copied && { background: '#ecfdf5', color: '#059669', borderColor: '#a7f3d0' }">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                                <span x-show="!copied">Copy</span>
                                <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span x-show="copied" x-cloak>Copied!</span>
                            </button>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 hover:shadow-sm"
                                style="background: rgba(37,211,102,0.1); color: #128C7E; border: 1px solid rgba(37,211,102,0.25);">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                                Message
                            </a>
                        </div>
                    </div>

                    {{-- Email Card --}}
                    <div x-data="{ copied: false }" class="rounded-2xl p-5 transition-shadow duration-300 hover:shadow-lg"
                         style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(10,46,35,0.06), 0 4px 12px rgba(10,46,35,0.04);">
                        <div class="flex items-center justify-center w-11 h-11 rounded-full mb-4"
                             style="background: linear-gradient(135deg, rgba(201,168,76,0.15), rgba(201,168,76,0.08));">
                            <svg class="w-5 h-5" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #C9A84C;">Email</p>
                        <p class="text-sm leading-relaxed break-all mb-4" style="color: #333;">{{ $contactEmail }}</p>
                        <div class="flex flex-wrap gap-2">
                            <button @click="navigator.clipboard.writeText('{{ $contactEmail }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200"
                                style="background: #F7F2EA; color: #6B6B6B; border: 1px solid #EDE6D8;"
                                :style="copied && { background: '#ecfdf5', color: '#059669', borderColor: '#a7f3d0' }">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                                <span x-show="!copied">Copy</span>
                                <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span x-show="copied" x-cloak>Copied!</span>
                            </button>
                            <a href="mailto:{{ $contactEmail }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 hover:shadow-sm"
                                style="background: #F7F2EA; color: #6B6B6B; border: 1px solid #EDE6D8;">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51"/></svg>
                                Send Email
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Opening Hours Card --}}
                <div class="rounded-2xl p-6 transition-shadow duration-300 hover:shadow-lg"
                     style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(10,46,35,0.06), 0 4px 12px rgba(10,46,35,0.04);">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex items-center justify-center w-11 h-11 rounded-full flex-shrink-0"
                             style="background: linear-gradient(135deg, rgba(201,168,76,0.15), rgba(201,168,76,0.08));">
                            <svg class="w-5 h-5" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold" style="color: #0A2E23;">Opening Hours</h3>
                            <p class="text-xs" style="color: #6B6B6B;">When you can visit us</p>
                        </div>
                    </div>

                    <div class="rounded-xl overflow-hidden" style="border: 1px solid #F0E8DB;">
                        @foreach([['Mon - Thu', $hoursMonThu, false], ['Friday', $hoursFri, false], ['Saturday', $hoursSat, false], ['Sunday', $hoursSun, true]] as [$day, $hours, $isLast])
                            <div class="flex justify-between items-center px-4 py-3 {{ !$isLast ? 'border-b' : '' }}"
                                 style="border-color: #F0E8DB; {{ $loop->index % 2 === 0 ? 'background: #FDFBF7;' : 'background: #FFFFFF;' }}">
                                <span class="text-sm" style="color: #6B6B6B;">{{ $day }}</span>
                                <span class="text-sm font-semibold {{ $hours === 'Closed' ? 'text-red-400' : '' }}" style="{{ $hours !== 'Closed' ? 'color: #0A2E23;' : '' }}">{{ $hours }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tip --}}
                    <div class="mt-4 flex items-start gap-2.5 p-3 rounded-xl" style="background: rgba(37,211,102,0.06); border: 1px solid rgba(37,211,102,0.15);">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: #128C7E;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        <p class="text-xs leading-relaxed" style="color: #128C7E;">For urgent assistance outside hours, WhatsApp is the fastest option.</p>
                    </div>
                </div>

                {{-- Google Maps Embed --}}
                @php
                    $mapRaw = \App\Models\Setting::get('map_embed_url', '');
                    // Extract src URL if user pasted full <iframe> tag
                    if (preg_match('/src=["\']([^"\']+)["\']/', $mapRaw, $m)) {
                        $mapSrc = $m[1];
                    } elseif (str_starts_with(trim($mapRaw), 'http')) {
                        $mapSrc = trim($mapRaw);
                    } else {
                        $mapSrc = '';
                    }
                @endphp
                <div class="rounded-2xl overflow-hidden transition-shadow duration-300 hover:shadow-lg"
                     style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(10,46,35,0.06), 0 4px 12px rgba(10,46,35,0.04);">
                    @if($mapSrc)
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                            <iframe src="{{ $mapSrc }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16 px-6 text-center" style="background: #FDFBF7;">
                            <div class="flex items-center justify-center w-14 h-14 rounded-full mb-4"
                                 style="background: linear-gradient(135deg, rgba(201,168,76,0.15), rgba(201,168,76,0.08));">
                                <svg class="w-7 h-7" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium" style="color: #0A2E23;">Map Coming Soon</p>
                            <p class="text-xs mt-1" style="color: #6B6B6B;">Use the address above to find us on Google Maps</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- RIGHT COLUMN: Contact Form                    --}}
            {{-- ============================================= --}}
            <div>
                <div class="rounded-2xl p-6 md:p-8 lg:sticky lg:top-8 transition-shadow duration-300 hover:shadow-lg"
                     style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(10,46,35,0.06), 0 4px 12px rgba(10,46,35,0.04); border-top: 3px solid #C9A84C;">

                    @if($formSubmitted)
                        {{-- Success State --}}
                        <div class="py-12 text-center">
                            <div class="flex items-center justify-center w-16 h-16 rounded-full mx-auto mb-5"
                                 style="background: linear-gradient(135deg, rgba(34,197,94,0.15), rgba(34,197,94,0.05));">
                                <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2" style="color: #0A2E23;">Message Sent!</h3>
                            <p class="text-sm leading-relaxed" style="color: #6B6B6B; max-width: 320px; margin: 0 auto;">
                                Thank you for reaching out. We will review your message and get back to you as soon as possible.
                            </p>
                            <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium" style="background: #F7F2EA; color: #6B6B6B;">
                                <svg class="w-4 h-4" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Typical response within 24 hours
                            </div>
                        </div>
                    @else
                        {{-- Form Header --}}
                        <div class="mb-6">
                            <h2 class="text-xl font-bold mb-1" style="color: #0A2E23;">Send Us a Message</h2>
                            <p class="text-sm" style="color: #6B6B6B;">Fill out the form below and we will get back to you shortly.</p>
                        </div>

                        <form wire:submit="submitForm" class="space-y-5">
                            {{-- Name --}}
                            <div>
                                <label for="formName" class="block text-sm font-medium mb-1.5" style="color: #0A2E23;">
                                    Full Name <span style="color: #C9A84C;">*</span>
                                </label>
                                <input type="text" id="formName" wire:model="formName" placeholder="Your full name"
                                    class="w-full rounded-xl px-4 py-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2"
                                    style="background: #FDFBF7; border: 1px solid #E8DFD0; color: #0A2E23; --tw-ring-color: rgba(201,168,76,0.3);">
                                @error('formName') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email + Phone row --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="formEmail" class="block text-sm font-medium mb-1.5" style="color: #0A2E23;">
                                        Email <span style="color: #C9A84C;">*</span>
                                    </label>
                                    <input type="email" id="formEmail" wire:model="formEmail" placeholder="your@email.com"
                                        class="w-full rounded-xl px-4 py-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2"
                                        style="background: #FDFBF7; border: 1px solid #E8DFD0; color: #0A2E23; --tw-ring-color: rgba(201,168,76,0.3);">
                                    @error('formEmail') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="formPhone" class="block text-sm font-medium mb-1.5" style="color: #0A2E23;">
                                        Phone
                                    </label>
                                    <input type="text" id="formPhone" wire:model="formPhone" placeholder="+92 300 1234567"
                                        class="w-full rounded-xl px-4 py-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2"
                                        style="background: #FDFBF7; border: 1px solid #E8DFD0; color: #0A2E23; --tw-ring-color: rgba(201,168,76,0.3);">
                                    @error('formPhone') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Subject --}}
                            <div>
                                <label for="formSubject" class="block text-sm font-medium mb-1.5" style="color: #0A2E23;">
                                    Subject
                                </label>
                                <input type="text" id="formSubject" wire:model="formSubject" placeholder="What is this about?"
                                    class="w-full rounded-xl px-4 py-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2"
                                    style="background: #FDFBF7; border: 1px solid #E8DFD0; color: #0A2E23; --tw-ring-color: rgba(201,168,76,0.3);">
                                @error('formSubject') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            {{-- Message --}}
                            <div>
                                <label for="formMessage" class="block text-sm font-medium mb-1.5" style="color: #0A2E23;">
                                    Message <span style="color: #C9A84C;">*</span>
                                </label>
                                <textarea id="formMessage" wire:model="formMessage" rows="5" placeholder="Tell us how we can help you..."
                                    class="w-full rounded-xl px-4 py-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2 resize-none"
                                    style="background: #FDFBF7; border: 1px solid #E8DFD0; color: #0A2E23; --tw-ring-color: rgba(201,168,76,0.3);"></textarea>
                                @error('formMessage') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-sm font-semibold text-white transition-all duration-300 hover:shadow-lg active:scale-[0.98]"
                                style="background: linear-gradient(135deg, #C9A84C, #B8943F, #C9A84C); box-shadow: 0 2px 8px rgba(201,168,76,0.3);"
                                wire:loading.attr="disabled"
                                wire:loading.style="opacity: 0.7; cursor: wait;">
                                <span wire:loading.remove class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                                    </svg>
                                    Send Message
                                </span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>

                            {{-- Privacy note --}}
                            <p class="text-center text-xs" style="color: #999;">
                                <svg class="w-3 h-3 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                Your information is secure and will never be shared.
                            </p>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Inline style for focus ring color override --}}
    <style>
        input:focus, textarea:focus {
            border-color: #C9A84C !important;
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.15) !important;
        }
        /* Make map iframe fill its container */
        .rounded-2xl iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
            border-radius: 0 0 1rem 1rem;
        }
    </style>
</div>
