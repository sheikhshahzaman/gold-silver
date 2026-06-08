@if($mobile)
    {{-- Mobile bottom-nav tab variant --}}
    <a href="/cart"
       class="relative flex flex-col items-center justify-center flex-1 py-1 {{ request()->is('cart') ? 'text-gold' : 'text-white/60' }}">
        <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
        </svg>
        <span class="text-[10px] font-medium">Cart</span>
        @if($count > 0)
            <span wire:key="cart-badge-mobile-{{ $count }}"
                  class="absolute top-0 right-[20%] min-w-[16px] h-[16px] px-1 rounded-full flex items-center justify-center text-[9px] font-bold leading-none"
                  style="background: #E53935; color: white;">
                {{ $count > 9 ? '9+' : $count }}
            </span>
        @endif
    </a>
@else
    {{-- Header pill — matches the Scan QR / Live Rates pill geometry exactly --}}
    <a href="/cart" title="Your cart"
       class="relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-medium transition-all hover:scale-105"
       style="background: rgba(201,168,76,0.1); border: 1px solid #C9A84C; color: #E8C96A;">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
        </svg>
        Cart
        @if($count > 0)
            <span wire:key="cart-badge-header-{{ $count }}"
                  class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold leading-none"
                  style="background: #E53935; color: white;">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </a>
@endif
