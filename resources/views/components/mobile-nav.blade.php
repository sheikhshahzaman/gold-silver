{{-- Mobile Bottom Navigation --}}
@php
    $waNumber = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('contact_whatsapp') ?? '');
    $waHref = $waNumber ? 'https://wa.me/'.$waNumber : '#';
@endphp
<nav class="fixed bottom-0 left-0 right-0 z-50 bg-emerald-900 border-t border-gold/20 safe-area-inset-bottom">
    <div class="flex items-end justify-around px-2 pt-2 pb-2">

        {{-- Scan QR --}}
        <a href="/scan" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->is('scan') ? 'text-gold' : 'text-white/60' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 14.25h1.5v1.5h-1.5v-1.5zM16.5 14.25h1.5v1.5h-1.5v-1.5zM19.5 14.25h.75v1.5h-.75v-1.5zM13.5 17.25h1.5v1.5h-1.5v-1.5zM13.5 20.25h1.5v.75h-1.5v-.75zM16.5 17.25h3v3h-3v-3z" />
            </svg>
            <span class="text-[10px] font-medium">Scan</span>
        </a>

        {{-- Shop --}}
        <a href="/products" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->is('products*') ? 'text-gold' : 'text-white/60' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
            </svg>
            <span class="text-[10px] font-medium">Shop</span>
        </a>

        {{-- Spot / Live (Center - Raised) --}}
        <a href="/live" class="flex flex-col items-center justify-center flex-1 -mt-4">
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-gold-light via-gold to-gold-dark shadow-lg shadow-gold/25 mb-0.5">
                <svg class="w-7 h-7 text-emerald-950" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
            </div>
            <span class="text-[10px] font-medium {{ request()->is('live') ? 'text-gold' : 'text-white/60' }}">Spot</span>
        </a>

        {{-- WhatsApp --}}
        <a href="{{ $waHref }}" @if($waNumber) target="_blank" rel="noopener" @endif class="flex flex-col items-center justify-center flex-1 py-1 text-white/60">
            <svg class="w-5 h-5 mb-0.5" viewBox="0 0 24 24" fill="currentColor" style="color: #25D366;">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
            </svg>
            <span class="text-[10px] font-medium">WhatsApp</span>
        </a>

        {{-- More --}}
        <a href="/more" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->is('more') ? 'text-gold' : 'text-white/60' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
            <span class="text-[10px] font-medium">More</span>
        </a>

    </div>
</nav>
