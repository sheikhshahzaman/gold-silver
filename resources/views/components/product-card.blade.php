{{-- Premium product card, shared by the home shop section and the products
     page. Must be rendered inside a Livewire component that exposes
     addToCart(int $productId) — the wire:click binds to that ancestor.

     Props:
       prod          App\Models\Product (with productCategory loaded)
       goldPrices    array  live gold price matrix
       silverPrices  array  live silver price matrix
       added         bool   flash state after Add to Cart
       index         int    position in the grid (staggers the reveal)
       detailed      bool   also show category chip + description excerpt --}}
@props(['prod', 'goldPrices' => [], 'silverPrices' => [], 'added' => false, 'index' => 0, 'detailed' => false])

@php
    $placeholderIcons = [
        'bars' => 'M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3',
        'coins' => 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375',
        'jewelry' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z',
    ];
    $phKey = str_replace('silver_', '', $prod->category ?? 'bars');
    $phIcon = $placeholderIcons[$phKey] ?? $placeholderIcons['bars'];
    $prodBg = $prod->metal === 'silver' ? 'linear-gradient(140deg,#F2F2F2,#D5D5D5 60%,#C2C2C2)' : 'linear-gradient(140deg,#FBF0D0,#F5E08B 60%,#EBCB66)';

    // Central resolver: exact board rows or tola-derived weights (ounce,
    // 2.5g, half tola...) — see Cart::unitQuoteFor.
    $liveQuote = app(\App\Services\Cart::class)->unitQuoteFor($prod);
    $livePrice = $liveQuote['buy'] ?? null;
    $liveSell = $liveQuote['sell'] ?? null;

    $isPurchasable = ($prod->price_type === 'fixed' && $prod->fixed_price)
        || ($prod->price_type === 'live' && $prod->price_key && $livePrice !== null);
@endphp

<div wire:key="prod-card-{{ $prod->id }}" data-reveal data-reveal-delay="{{ 80 + ($index % 4) * 80 }}" class="product-card group flex flex-col">
    <div class="product-media relative overflow-hidden">
        @if($isPurchasable && $prod->discount_label)
            <span class="absolute top-2.5 left-2.5 z-10 px-2.5 py-1 rounded-full text-[10px] font-bold text-white shadow-md" style="background: linear-gradient(135deg, #E53935, #C62828);">{{ $prod->discount_label }}</span>
        @endif
        <span class="absolute top-2.5 right-2.5 z-10 w-7 h-7 rounded-full flex items-center justify-center text-[9px] font-bold shadow-sm" style="{{ $prod->metal === 'silver' ? 'background: linear-gradient(135deg, #E0E0E0, #9E9E9E); color: #1A1A1A;' : 'background: linear-gradient(135deg, #E8C96A, #A67922); color: #0A2E23;' }}">{{ $prod->metal === 'silver' ? 'Ag' : 'Au' }}</span>
        @if($prod->image)
            <div class="h-40 sm:h-44 flex items-center justify-center overflow-hidden" style="background: {{ $prodBg }};">
                <img src="{{ Storage::disk('public')->url($prod->image) }}" alt="{{ $prod->name }}" loading="lazy" class="product-img h-full w-full object-cover">
            </div>
        @else
            <div class="h-40 sm:h-44 flex items-center justify-center" style="background: {{ $prodBg }};">
                <svg class="product-img w-14 h-14 opacity-50" style="color: {{ $prod->metal === 'silver' ? '#6B6B6B' : '#8B6914' }};" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $phIcon }}"/></svg>
            </div>
        @endif
    </div>
    <div class="p-3.5 flex flex-col flex-1">
        <div class="text-sm font-semibold mb-0.5 truncate" style="color: #1A1A1A;">{{ $prod->name }}</div>
        <div class="text-[11px] {{ $detailed ? 'mb-1.5' : 'mb-2.5' }}" style="color: #8A8270;">{{ $prod->weight }}</div>

        @if($detailed)
            @if($prod->productCategory)
                <span class="self-start inline-block text-[10px] px-2.5 py-0.5 rounded-full mb-2" style="background: #F2EBD9; color: #8B6914; border: 1px solid rgba(201,168,76,0.25);">{{ $prod->productCategory->name }}</span>
            @endif
            @if($prod->description)
                <p class="text-xs leading-relaxed mb-2.5" style="color: #A39A85;">{{ Str::limit($prod->description, 80) }}</p>
            @endif
        @endif

        <div class="min-h-[40px] mb-3 mt-auto">
            @if($prod->price_type === 'fixed' && $prod->fixed_price)
                @if($prod->hasActiveDiscount())
                    <div class="text-[11px] line-through" style="color: #B0A890;">Rs {{ number_format($prod->fixed_price) }}</div>
                    <div class="text-base font-bold tabular-nums" style="color: #E53935;">Rs {{ number_format($prod->applyDiscount($prod->fixed_price)) }}</div>
                @else
                    <div class="text-base font-bold tabular-nums" style="color: #0D3D1F;">Rs {{ number_format($prod->fixed_price) }}</div>
                @endif
            @elseif($livePrice)
                @if($prod->hasActiveDiscount())
                    <div class="text-[11px] line-through" style="color: #B0A890;">Rs {{ number_format($livePrice) }}</div>
                    <div class="text-base font-bold tabular-nums" style="color: #E53935;">Rs {{ number_format($prod->applyDiscount($livePrice)) }}</div>
                @else
                    <div class="text-base font-bold tabular-nums" style="color: #0D3D1F;">Rs {{ number_format($livePrice) }}</div>
                    @if($liveSell)
                        <div class="text-[11px] tabular-nums" style="color: #8A8270;">Sell: Rs {{ number_format($liveSell) }}</div>
                    @endif
                @endif
            @else
                <div class="text-sm font-semibold" style="color: #0D3D1F;">Price unavailable</div>
                <div class="text-[11px]" style="color: #8A8270;">Please check again shortly</div>
            @endif
        </div>

        <button wire:click="addToCart({{ $prod->id }})"
                wire:loading.attr="disabled"
                wire:target="addToCart({{ $prod->id }})"
                @disabled(!$isPurchasable)
                class="btn-cart {{ $added ? 'is-added' : '' }}">
            <span wire:loading.remove wire:target="addToCart({{ $prod->id }})" class="inline-flex items-center gap-1.5">
                @if($added)
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Added to Cart
                @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    {{ $isPurchasable ? 'Add to Cart' : 'Unavailable' }}
                @endif
            </span>
            <span wire:loading wire:target="addToCart({{ $prod->id }})" class="inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Adding...
            </span>
        </button>
    </div>
</div>
