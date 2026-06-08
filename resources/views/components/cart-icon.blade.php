@props([
    'showLabel' => false,
    'variant' => 'dark', // 'dark' for ivory headers, 'light' for dark backgrounds
])
@php
    $count = app(\App\Services\Cart::class)->count();
    $iconColor = $variant === 'light' ? 'text-white/80 hover:text-gold-light' : '';
    $iconStyle = $variant === 'dark' ? 'color: #E8C96A;' : '';
@endphp

<a href="/cart" wire:navigate
   title="Your cart"
   class="relative inline-flex items-center justify-center {{ $showLabel ? 'gap-2 px-3 py-1.5' : 'w-9 h-9' }} rounded-full transition-colors {{ $iconColor }}"
   @if($variant === 'dark') style="background: rgba(201,168,76,0.1); border: 1px solid #C9A84C; {{ $iconStyle }}" @endif
>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
    </svg>
    @if($showLabel)
        <span class="text-[11px] font-medium">Cart</span>
    @endif

    @if($count > 0)
        <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full flex items-center justify-center text-[10px] font-bold leading-none"
              style="background: #E53935; color: white;">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</a>
