<div class="max-w-2xl mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold mb-1" style="color: #0A2E23;">Contact Us</h1>
        <p class="text-sm" style="color: #888;">Get in touch with our team</p>
    </div>

    <div class="space-y-4">
        {{-- Address Card --}}
        <div class="glass-card p-5" x-data="{ copied: false }">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(198,150,60,0.15);">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-sm mb-1" style="color: #0A2E23;">Address</h3>
                    <p class="text-sm" style="color: #555;">{{ $contactAddress }}</p>
                </div>
            </div>
            <div class="flex gap-2 mt-4 ml-14">
                <button @click="navigator.clipboard.writeText('{{ $contactAddress }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="relative px-3 py-1.5 rounded-lg text-xs font-medium transition-colors" style="background: #F0E8DB; color: #555;">
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied" x-cloak class="text-green-600">Copied!</span>
                </button>
                <a href="https://maps.google.com/?q={{ urlencode($contactAddress) }}" target="_blank" rel="noopener"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors" style="background: #F0E8DB; color: #555;">
                    Open
                </a>
            </div>
        </div>

        {{-- Phone Card --}}
        <div class="glass-card p-5" x-data="{ copied: false }">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(198,150,60,0.15);">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-sm mb-1" style="color: #0A2E23;">Phone</h3>
                    <p class="text-sm" style="color: #555;">{{ $contactPhone }}</p>
                </div>
            </div>
            <div class="flex gap-2 mt-4 ml-14">
                <button @click="navigator.clipboard.writeText('{{ $contactPhone }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="relative px-3 py-1.5 rounded-lg text-xs font-medium" style="background: #F0E8DB; color: #555;">
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied" x-cloak class="text-green-600">Copied!</span>
                </button>
                <a href="tel:{{ $contactPhone }}" class="px-3 py-1.5 rounded-lg text-xs font-medium" style="background: #F0E8DB; color: #555;">Call</a>
            </div>
        </div>

        {{-- WhatsApp Card --}}
        <div class="glass-card p-5" x-data="{ copied: false }">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-green-500/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-sm mb-1" style="color: #0A2E23;">WhatsApp</h3>
                    <p class="text-sm" style="color: #555;">{{ $contactWhatsapp }}</p>
                </div>
            </div>
            <div class="flex gap-2 mt-4 ml-14">
                <button @click="navigator.clipboard.writeText('{{ $contactWhatsapp }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="relative px-3 py-1.5 rounded-lg text-xs font-medium" style="background: #F0E8DB; color: #555;">
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied" x-cloak class="text-green-600">Copied!</span>
                </button>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" rel="noopener"
                    class="px-3 py-1.5 rounded-lg bg-green-500/15 text-green-700 text-xs font-medium hover:bg-green-500/25 transition-colors">Message</a>
            </div>
        </div>

        {{-- Email Card --}}
        <div class="glass-card p-5" x-data="{ copied: false }">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(198,150,60,0.15);">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-sm mb-1" style="color: #0A2E23;">Email</h3>
                    <p class="text-sm break-all" style="color: #555;">{{ $contactEmail }}</p>
                </div>
            </div>
            <div class="flex gap-2 mt-4 ml-14">
                <button @click="navigator.clipboard.writeText('{{ $contactEmail }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="relative px-3 py-1.5 rounded-lg text-xs font-medium" style="background: #F0E8DB; color: #555;">
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied" x-cloak class="text-green-600">Copied!</span>
                </button>
                <a href="mailto:{{ $contactEmail }}" class="px-3 py-1.5 rounded-lg text-xs font-medium" style="background: #F0E8DB; color: #555;">Email</a>
            </div>
        </div>

        {{-- Opening Hours --}}
        <div class="glass-card p-5">
            <div class="flex items-start gap-4 mb-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(198,150,60,0.15);">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-sm mb-1" style="color: #0A2E23;">Opening Hours</h3>
                </div>
            </div>
            <div class="ml-14 space-y-2">
                @foreach([['Mon - Thu', $hoursMonThu], ['Friday', $hoursFri], ['Saturday', $hoursSat], ['Sunday', $hoursSun]] as $i => [$day, $hours])
                    <div class="flex justify-between items-center py-1.5 {{ $i < 3 ? 'border-b' : '' }}" style="border-color: #F0E8DB;">
                        <span class="text-sm" style="color: #888;">{{ $day }}</span>
                        <span class="text-sm font-medium" style="color: #0A2E23;">{{ $hours }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 ml-14 flex items-start gap-2 p-3 rounded-lg bg-green-500/10 border border-green-500/20">
                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <p class="text-green-700 text-xs">For urgent assistance, WhatsApp is the fastest option</p>
            </div>
        </div>

        {{-- Contact Form --}}
        <div class="glass-card p-5">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(198,150,60,0.15);">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-sm mb-1" style="color: #0A2E23;">Send a Message</h3>
                    <p class="text-xs" style="color: #999;">We will get back to you soon</p>
                </div>
            </div>

            @if($formSubmitted)
                <div class="ml-14 p-4 rounded-lg bg-green-500/15 border border-green-500/25 text-center">
                    <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 font-semibold text-sm">Message sent successfully!</p>
                    <p class="text-green-600 text-xs mt-1">We will respond as soon as possible.</p>
                </div>
            @else
                <form wire:submit="submitForm" class="ml-14 space-y-4">
                    @foreach([
                        ['formName', 'Name', 'text', 'Your full name', true],
                        ['formEmail', 'Email', 'email', 'your@email.com', true],
                        ['formPhone', 'Phone', 'text', '+92 300 1234567', false],
                        ['formSubject', 'Subject', 'text', 'What is this about?', false],
                    ] as [$field, $label, $type, $placeholder, $required])
                        <div>
                            <label for="{{ $field }}" class="block text-xs font-medium mb-1.5" style="color: #555;">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
                            <input type="{{ $type }}" id="{{ $field }}" wire:model="{{ $field }}" placeholder="{{ $placeholder }}"
                                class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold/30 transition-colors"
                                style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;">
                            @error($field) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                    <div>
                        <label for="formMessage" class="block text-xs font-medium mb-1.5" style="color: #555;">Message <span class="text-red-500">*</span></label>
                        <textarea id="formMessage" wire:model="formMessage" rows="4" placeholder="Your message..."
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold/30 transition-colors resize-none"
                            style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;"></textarea>
                        @error('formMessage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn-gold w-full text-sm py-3" wire:loading.attr="disabled">
                        <span wire:loading.remove>Send Message</span>
                        <span wire:loading>
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
