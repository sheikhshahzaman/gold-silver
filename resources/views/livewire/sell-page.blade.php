<div class="max-w-2xl mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-2xl md:text-3xl font-bold" style="color: #0A2E23;">Sell</h1>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full" style="background: #0F3D2E;">
                <span class="live-dot"></span>
                <span class="text-green-400 text-xs font-bold">LIVE</span>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <p class="text-sm" style="color: #666;">
                Sell - Updated (PKT):
                @if($lastUpdated)
                    {{ \Carbon\Carbon::parse($lastUpdated)->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                @else
                    --
                @endif
            </p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background: rgba(198,150,60,0.15); color: #A67922; border: 1px solid rgba(198,150,60,0.25);">
                PKR
            </span>
        </div>
    </div>

    {{-- Step Wizard --}}
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center gap-0">
            <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold text-sm transition-all duration-300
                {{ $step >= 1 ? 'bg-gold text-white' : 'text-gray-400' }}" @if($step < 1) style="background: #E8DFD0;" @endif>1</div>
            <div class="w-20 h-0.5 transition-all duration-300 {{ $step >= 2 ? 'bg-gold' : '' }}" @if($step < 2) style="background: #E8DFD0;" @endif></div>
            <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold text-sm transition-all duration-300
                {{ $step >= 2 ? 'bg-gold text-white' : 'text-gray-400' }}" @if($step < 2) style="background: #E8DFD0;" @endif>2</div>
        </div>
    </div>

    @if($step === 1)
        <div class="space-y-6">
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold mb-1" style="color: #0A2E23;">Step 1: Choose metal</h2>
                <p class="text-sm" style="color: #888;">Gold, Silver</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button wire:click="selectMetal('gold')"
                    class="glass-card p-6 text-center transition-all duration-300 cursor-pointer {{ $selectedMetal === 'gold' ? '!border-gold' : '' }}">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-12 h-12 {{ $selectedMetal === 'gold' ? 'text-gold' : '' }}" style="{{ $selectedMetal !== 'gold' ? 'color: #999;' : '' }}" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                        </svg>
                        <span class="text-lg font-semibold {{ $selectedMetal === 'gold' ? 'text-gold' : '' }}" style="{{ $selectedMetal !== 'gold' ? 'color: #555;' : '' }}">Gold</span>
                    </div>
                </button>
                <button wire:click="selectMetal('silver')"
                    class="glass-card p-6 text-center transition-all duration-300 cursor-pointer {{ $selectedMetal === 'silver' ? '!border-gold' : '' }}">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-12 h-12 {{ $selectedMetal === 'silver' ? 'text-gold' : '' }}" style="{{ $selectedMetal !== 'silver' ? 'color: #999;' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                        <span class="text-lg font-semibold {{ $selectedMetal === 'silver' ? 'text-gold' : '' }}" style="{{ $selectedMetal !== 'silver' ? 'color: #555;' : '' }}">Silver</span>
                    </div>
                </button>
            </div>

            @if($selectedMetal === 'gold')
                <div class="space-y-3">
                    <label class="text-sm font-medium" style="color: #555;">Select Karat</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['24k' => '24K', 'rawa' => 'Rawa', '22k' => '22K', '21k' => '21K', '18k' => '18K'] as $key => $label)
                            <button wire:click="selectKarat('{{ $key }}')"
                                class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 {{ $selectedKarat === $key ? 'bg-gold text-white' : '' }}"
                                @if($selectedKarat !== $key) style="background: #F0E8DB; color: #555;" @endif>
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <button wire:click="nextStep" class="btn-gold w-full text-base py-3.5 mt-4">
                Continue
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
        </div>

    @else
        <div class="space-y-6">
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold mb-1" style="color: #0A2E23;">Step 2: Quantity</h2>
                <p class="text-sm" style="color: #888;">See SELL price instantly</p>
            </div>

            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium" style="background: rgba(198,150,60,0.15); color: #A67922; border: 1px solid rgba(198,150,60,0.25);">
                    @if($selectedMetal === 'gold')
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                        Gold {{ strtoupper($selectedKarat) }}
                    @else
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/></svg>
                        Silver
                    @endif
                </span>
            </div>

            <div class="space-y-3">
                <label class="text-sm font-medium" style="color: #555;">Select Unit</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['tola' => 'Tola', 'gram' => 'Gram', '10_gram' => '10 Gram', 'kg' => 'KG'] as $key => $label)
                        <button wire:click="selectUnit('{{ $key }}')"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 {{ $selectedUnit === $key ? 'bg-gold text-white' : '' }}"
                            @if($selectedUnit !== $key) style="background: #F0E8DB; color: #555;" @endif>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-sm font-medium" style="color: #555;">Quantity</label>
                <div class="flex items-center gap-3">
                    <button wire:click="decrementQuantity" class="w-12 h-12 rounded-xl transition-colors flex items-center justify-center" style="background: #F0E8DB; color: #0A2E23;">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                    </button>
                    <input type="number" wire:model.live.debounce.300ms="quantity" min="1" max="9999"
                        class="flex-1 rounded-xl px-4 py-3 text-center text-xl font-bold focus:outline-none focus:ring-2 focus:ring-gold/30 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;">
                    <button wire:click="incrementQuantity" class="w-12 h-12 rounded-xl transition-colors flex items-center justify-center" style="background: #F0E8DB; color: #0A2E23;">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </button>
                </div>
            </div>

            <div class="glass-card p-6 text-center">
                <p class="text-sm mb-2" style="color: #888;">Total Sell Price</p>
                @if($calculatedPrice !== null)
                    <p class="text-3xl md:text-4xl font-bold text-gold">Rs {{ number_format($calculatedPrice, 0) }}</p>
                    <p class="text-xs mt-2" style="color: #999;">
                        {{ $quantity }} x @if($selectedMetal === 'gold') Gold {{ strtoupper($selectedKarat) }} @else Silver @endif
                        ({{ str_replace('_', ' ', ucfirst($selectedUnit)) }})
                    </p>
                @else
                    <p class="text-2xl font-bold" style="color: #ccc;">Price unavailable</p>
                @endif
            </div>

            <div class="flex gap-3">
                <button wire:click="prevStep" class="px-6 py-3.5 rounded-lg font-semibold" style="background: #F0E8DB; color: #0A2E23;">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                </button>
                <button wire:click="placeOrder" class="btn-gold flex-1 text-base py-3.5" @if($calculatedPrice === null) disabled style="opacity: 0.5; cursor: not-allowed;" @endif>
                    Place Order
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </div>
        </div>
    @endif
</div>
