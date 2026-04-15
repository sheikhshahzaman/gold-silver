<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg mx-auto">

        {{-- Header --}}
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-block">
                <h1 class="text-xl font-bold text-gray-900">Islamabad Bullion Exchange</h1>
            </a>
            <p class="text-sm text-gray-500 mt-1">Product Verification System</p>
        </div>

        @if(!$valid)
            {{-- INVALID TOKEN --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-red-200">
                <div class="bg-red-600 px-6 py-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Verification Failed</h2>
                    <p class="text-red-100 mt-2">This QR code is not recognized</p>
                </div>
                <div class="px-6 py-6">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <h3 class="font-semibold text-red-800 mb-2">Warning</h3>
                        <p class="text-red-700 text-sm">This product may be counterfeit or the QR code has been tampered with. Please verify the product directly with Islamabad Bullion Exchange.</p>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('contact') }}" class="inline-flex items-center text-sm font-medium text-amber-600 hover:text-amber-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Contact Us for Verification
                        </a>
                    </div>
                </div>
            </div>

        @elseif($item->status === 'sold')
            {{-- SOLD ITEM --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-amber-200">
                <div class="bg-amber-500 px-6 py-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-amber-400 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Authentic Product</h2>
                    <p class="text-amber-100 mt-2">This piece has been sold</p>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                        <p class="text-amber-800 font-medium">This piece was sold on {{ $item->sold_at?->format('M j, Y') }}</p>
                    </div>

                    <div class="border border-gray-200 rounded-xl divide-y divide-gray-100">
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Serial Number</span>
                            <span class="font-mono font-semibold text-gray-900 text-sm">{{ $item->serial_number }}</span>
                        </div>
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Product</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $item->product->name }}</span>
                        </div>
                        @if($item->product->metal)
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Metal</span>
                            <span class="font-medium text-gray-900 text-sm">{{ ucfirst($item->product->metal) }} {{ $item->product->karat }}</span>
                        </div>
                        @endif
                        @if($item->actual_weight)
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Weight</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $item->actual_weight }}g</span>
                        </div>
                        @endif
                        @if($item->purity_tested)
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Purity</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $item->purity_tested }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        @elseif($item->status === 'reserved')
            {{-- RESERVED ITEM --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-blue-200">
                <div class="bg-blue-600 px-6 py-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Authentic Product</h2>
                    <p class="text-blue-100 mt-2">Currently Reserved</p>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                        <p class="text-blue-800 font-medium">This piece is currently reserved for a customer</p>
                    </div>

                    <div class="border border-gray-200 rounded-xl divide-y divide-gray-100">
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Serial Number</span>
                            <span class="font-mono font-semibold text-gray-900 text-sm">{{ $item->serial_number }}</span>
                        </div>
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Product</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $item->product->name }}</span>
                        </div>
                        @if($item->product->metal)
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Metal</span>
                            <span class="font-medium text-gray-900 text-sm">{{ ucfirst($item->product->metal) }} {{ $item->product->karat }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        @else
            {{-- IN STOCK — VERIFIED AUTHENTIC --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-green-200">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Verified Authentic</h2>
                    <p class="text-green-100 mt-2">This product is genuine — from Islamabad Bullion Exchange</p>
                </div>
                <div class="px-6 py-6 space-y-4">
                    {{-- Product Details --}}
                    <div class="border border-gray-200 rounded-xl divide-y divide-gray-100">
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Serial Number</span>
                            <span class="font-mono font-semibold text-gray-900 text-sm">{{ $item->serial_number }}</span>
                        </div>
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Product</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $item->product->name }}</span>
                        </div>
                        @if($item->product->metal)
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Metal</span>
                            <span class="font-medium text-gray-900 text-sm">{{ ucfirst($item->product->metal) }} {{ $item->product->karat }}</span>
                        </div>
                        @endif
                        @if($item->actual_weight)
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Weight</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $item->actual_weight }}g</span>
                        </div>
                        @endif
                        @if($item->purity_tested)
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Purity</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $item->purity_tested }}</span>
                        </div>
                        @endif
                        @if($item->product->productCategory)
                        <div class="flex justify-between px-4 py-3">
                            <span class="text-gray-500 text-sm">Category</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $item->product->productCategory->name }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Live Pricing --}}
                    @if(!empty($prices) && isset($prices['sell']))
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-amber-600 font-medium uppercase tracking-wider">Current Market Price</p>
                                <p class="text-2xl font-bold text-amber-800 mt-1">Rs {{ number_format($prices['sell']) }}</p>
                            </div>
                            <div class="text-right">
                                <div class="w-10 h-10 bg-amber-200 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Claim Section --}}
                    @if($claimed)
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center">
                            <p class="text-gray-600 text-sm">This item has been claimed by a customer</p>
                        </div>
                    @elseif($showClaimForm)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <h3 class="font-semibold text-green-800 text-sm mb-3">Register as Owner</h3>
                            <p class="text-xs text-green-700 mb-3">Enter your phone number to claim this piece. This helps verify ownership if you resell later.</p>
                            <form wire:submit="claimItem" class="flex gap-2">
                                <input type="tel" wire:model="claimPhone" placeholder="03XX-XXXXXXX" class="flex-1 rounded-lg border-gray-300 text-sm text-gray-900 bg-white focus:border-green-500 focus:ring-green-500" required>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">Claim</button>
                            </form>
                            @error('claimPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <button wire:click="$set('showClaimForm', true)" class="w-full py-3 bg-green-600 text-white rounded-xl text-sm font-medium hover:bg-green-700 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Claim This Piece as Owner
                        </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="text-center mt-6">
            <p class="text-xs text-gray-400">
                Scan #{{ $item?->scan_count ?? 0 }}
                @if($item?->first_scanned_at)
                    &middot; First scanned {{ $item->first_scanned_at->diffForHumans() }}
                @endif
            </p>
            <a href="{{ route('home') }}" class="inline-block mt-3 text-sm text-amber-600 hover:text-amber-700 font-medium">
                Visit Islamabad Bullion Exchange
            </a>
        </div>
    </div>
</div>
