<div class="min-h-screen px-4 py-10" style="background: #FAF6EE;">
    <div class="max-w-5xl mx-auto">
        <div class="mb-8">
            <div class="section-kicker mb-2" style="color: #8B6914;">Buy Online</div>
            <h1 class="font-display text-4xl sm:text-5xl font-semibold" style="color: #1A1A1A;">Buy Calculator</h1>
            <p class="text-sm mt-3 max-w-2xl" style="color: #6B6B6B;">Select metal, size and quantity to estimate the current buy amount before contacting us.</p>
        </div>

        <div class="grid lg:grid-cols-[1.15fr_0.85fr] gap-5">
            <div class="glass-card p-5 sm:p-6">
                <div class="flex flex-col gap-6">
                    <div>
                        <label class="block text-sm font-semibold mb-3" style="color: #0A2E23;">Metal</label>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach($this->metals as $value => $label)
                                <button type="button" wire:click="selectMetal('{{ $value }}')"
                                    class="rounded-2xl px-4 py-3 text-sm font-semibold transition-all"
                                    style="{{ $selectedMetal === $value ? 'background: #0A2E23; color: #E8C96A; border: 1px solid #0A2E23;' : 'background: #F7F2EA; color: #5C5341; border: 1px solid #E8DFD0;' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if($selectedMetal === 'gold')
                        <div>
                            <label class="block text-sm font-semibold mb-3" style="color: #0A2E23;">Karat</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($this->goldKarats as $value => $label)
                                    <button type="button" wire:click="selectKarat('{{ $value }}')"
                                        class="rounded-full px-4 py-2 text-sm font-semibold transition-all"
                                        style="{{ $selectedKarat === $value ? 'background: #C6963C; color: #0A2E23; border: 1px solid #C6963C;' : 'background: #F7F2EA; color: #5C5341; border: 1px solid #E8DFD0;' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold mb-3" style="color: #0A2E23;">Size</label>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach($this->unitOptions as $value => $label)
                                <button type="button" wire:click="selectUnit('{{ $value }}')"
                                    class="rounded-2xl px-4 py-3 text-left text-sm font-semibold transition-all"
                                    style="{{ $selectedUnit === $value ? 'background: #0A2E23; color: #E8C96A; border: 1px solid #0A2E23;' : 'background: #F7F2EA; color: #5C5341; border: 1px solid #E8DFD0;' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="buy-calculator-quantity" class="block text-sm font-semibold mb-3" style="color: #0A2E23;">Quantity</label>
                        <input
                            id="buy-calculator-quantity"
                            type="text"
                            inputmode="decimal"
                            wire:model.live.debounce.250ms="quantity"
                            class="w-full rounded-2xl px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-gold/30"
                            style="background: #F7F2EA; border: 1px solid #E8DFD0; color: #0A2E23;"
                        >
                    </div>

                    <button type="button" wire:click="calculatePrice" class="btn-gold w-full sm:w-auto px-7 py-3">
                        Calculate Total
                    </button>
                </div>
            </div>

            <div class="glass-card p-5 sm:p-6 h-fit">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: #8B6914;">Estimate</p>
                        <h2 class="text-xl font-semibold mt-1" style="color: #0A2E23;">{{ $this->selectedLabel }}</h2>
                    </div>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(198,150,60,0.14); color: #8B6914;">PKR</span>
                </div>

                @if($calculatedPrice !== null && $unitPrice !== null)
                    <div class="rounded-2xl p-5 mb-5" style="background: #0A2E23;">
                        <p class="text-xs uppercase tracking-widest mb-2" style="color: #E8C96A;">Total amount</p>
                        <p class="text-3xl font-bold" style="color: #FFFFFF;">Rs {{ number_format($calculatedPrice, 0) }}</p>
                    </div>

                    <div class="space-y-3 text-sm mb-5">
                        <div class="flex justify-between gap-4">
                            <span style="color: #6B6B6B;">Unit price</span>
                            <span class="font-semibold text-right" style="color: #0A2E23;">Rs {{ number_format($unitPrice, 0) }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span style="color: #6B6B6B;">Quantity</span>
                            <span class="font-semibold text-right" style="color: #0A2E23;">{{ $quantity }}</span>
                        </div>
                        @if($lastUpdated)
                            <div class="flex justify-between gap-4">
                                <span style="color: #6B6B6B;">Updated</span>
                                <span class="font-semibold text-right" style="color: #0A2E23;">{{ \Illuminate\Support\Carbon::parse($lastUpdated)->format('d M Y, h:i A') }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="rounded-2xl p-5 mb-5" style="background: #F7F2EA; border: 1px solid #E8DFD0;">
                        <p class="font-semibold" style="color: #0A2E23;">Rate not available</p>
                        <p class="text-sm mt-1" style="color: #6B6B6B;">Please choose another size or update prices in admin.</p>
                    </div>
                @endif

                <div class="pt-5" style="border-top: 1px solid #E8DFD0;">
                    <h3 class="font-semibold mb-2" style="color: #0A2E23;">Contact us to buy</h3>
                    <p class="text-sm mb-4" style="color: #6B6B6B;">Send this estimate to our team and we will confirm availability.</p>

                    <div class="flex flex-col sm:flex-row lg:flex-col gap-3">
                        @if($this->whatsappUrl)
                            <a href="{{ $this->whatsappUrl }}" target="_blank" rel="noopener" class="btn-gold text-center px-5 py-3">WhatsApp</a>
                        @endif

                        @if($contactPhone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="text-center px-5 py-3 rounded-full font-semibold"
                                style="border: 1px solid #C6963C; color: #8B6914;">Call Us</a>
                        @endif
                    </div>

                    @if($contactAddress)
                        <p class="text-xs mt-4 leading-relaxed" style="color: #6B6B6B;">{{ $contactAddress }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
