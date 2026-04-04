@props([
    'title' => 'PakGold Rates - Live Gold & Silver Prices in Pakistan',
    'description' => 'Get live gold, silver and currency rates in Pakistan. Trusted bullion exchange rates updated in real-time.',
    'url' => url()->current(),
])

{{-- Basic Meta --}}
<meta name="description" content="{{ $description }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $url }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $url }}">
