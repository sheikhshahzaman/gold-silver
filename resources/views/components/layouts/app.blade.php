<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Islamabad Bullion Exchange' }}</title>

    <x-seo-meta
        :title="$title ?? 'Islamabad Bullion Exchange - Live Gold & Silver Prices in Pakistan'"
        :description="$description ?? 'Islamabad Bullion Exchange - Get live gold, silver and currency rates in Pakistan. Trusted bullion exchange rates updated in real-time.'"
        :url="$url ?? url()->current()"
    />

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cormorant:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-emerald-950 text-white min-h-screen flex flex-col antialiased">

    <x-header />

    {{-- Main Content --}}
    <main class="flex-1 pb-20 md:pb-0">
        {{ $slot }}
    </main>

    {{-- Footer (desktop only) --}}
    <div class="hidden md:block">
        <x-footer />
    </div>

    {{-- Mobile bottom navigation --}}
    <div class="md:hidden">
        <x-mobile-nav />
    </div>

    {{-- Floating draggable cart button (mobile only — desktop has the header pill).
         Drag it anywhere; it snaps to the nearest edge and remembers its spot.
         Tap opens the cart. Hidden on the cart page itself. --}}
    @unless(request()->is('cart'))
        <div class="md:hidden fixed z-[70] floating-cart-wrap"
             x-data="floatingCart"
             :style="posStyle"
             :class="{ 'is-dragging': dragging && moved }"
             @pointerdown="startDrag($event)"
             role="button"
             aria-label="Open your cart"
             tabindex="0"
             @keydown.enter="window.location.href = '/cart'"
             style="right: 14px; bottom: 96px;">
            <span class="floating-cart-ring" aria-hidden="true"></span>
            <div class="floating-cart-btn">
                <livewire:cart-icon-widget :floating="true" />
            </div>
        </div>
    @endunless

    @livewireScripts
</body>
</html>
