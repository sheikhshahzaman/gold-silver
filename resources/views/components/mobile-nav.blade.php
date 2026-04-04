{{-- Mobile Bottom Navigation --}}
<nav class="fixed bottom-0 left-0 right-0 z-50 bg-emerald-900 border-t border-gold/20 safe-area-inset-bottom">
    <div class="flex items-end justify-around px-2 pt-2 pb-2">

        {{-- Contact Us --}}
        <a href="/contact" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->is('contact') ? 'text-gold' : 'text-white/60' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
            </svg>
            <span class="text-[10px] font-medium">Contact</span>
        </a>

        {{-- Buy --}}
        <a href="/buy" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->is('buy') ? 'text-gold' : 'text-white/60' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
            <span class="text-[10px] font-medium">Buy</span>
        </a>

        {{-- Spot / Home (Center - Raised) --}}
        <a href="/" class="flex flex-col items-center justify-center flex-1 -mt-4">
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-gold-light via-gold to-gold-dark shadow-lg shadow-gold/25 mb-0.5">
                <svg class="w-7 h-7 text-emerald-950" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
            </div>
            <span class="text-[10px] font-medium {{ request()->is('/') ? 'text-gold' : 'text-white/60' }}">Spot</span>
        </a>

        {{-- Sell --}}
        <a href="/sell" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->is('sell') ? 'text-gold' : 'text-white/60' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
            </svg>
            <span class="text-[10px] font-medium">Sell</span>
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
