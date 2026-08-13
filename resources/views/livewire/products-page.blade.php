<div wire:poll.5s>
    <section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(180deg, #FAF6EE 0%, #F4EDDD 100%); min-height: 80vh;">
        <div class="max-w-6xl mx-auto">

            <div data-reveal class="mb-8">
                <div class="section-kicker mb-2" style="color: #8B6914;">Buy Online</div>
                <h1 class="font-display font-semibold text-4xl sm:text-5xl leading-tight mb-3" style="color: #1A1A1A;">Shop Gold &amp; Silver <span class="italic" style="color: #8B6914;">Products</span></h1>
                <p class="text-sm max-w-xl" style="color: #6B6B6B;">Browse our collection of certified gold, silver bullion and jewelry products.</p>
            </div>

            {{-- Category Tabs (dynamic from DB) --}}
            <div data-reveal data-reveal-delay="80" class="flex gap-2 mb-8 overflow-x-auto no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap">
                <button
                    wire:click="setCategory('all')"
                    class="shrink-0 px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300
                        {{ $categoryId === 'all' ? 'shadow-[0_4px_12px_rgba(13,61,31,0.25)]' : 'hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(139,105,20,0.18)]' }}"
                    @if($categoryId === 'all')
                        style="background: linear-gradient(135deg, #0D3D1F, #165A42); color: #F4E2A0;"
                    @else
                        style="background: white; color: #5C5341; border: 1px solid #E3D9C2;"
                    @endif
                >
                    All
                </button>
                @foreach($categories as $cat)
                    <button
                        wire:click="setCategory('{{ $cat->id }}')"
                        class="shrink-0 px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300
                            {{ $categoryId === (string) $cat->id ? 'shadow-[0_4px_12px_rgba(13,61,31,0.25)]' : 'hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(139,105,20,0.18)]' }}"
                        @if($categoryId === (string) $cat->id)
                            style="background: linear-gradient(135deg, #0D3D1F, #165A42); color: #F4E2A0;"
                        @else
                            style="background: white; color: #5C5341; border: 1px solid #E3D9C2;"
                        @endif
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            @error('cart')
                <div class="mb-5 rounded-xl px-4 py-3 text-sm" style="background: #FFF3CD; color: #7A4F00; border: 1px solid #F5D77A;">
                    {{ $message }}
                </div>
            @enderror

            {{-- Products Grid --}}
            @if($products->count())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                    @foreach($products as $idx => $prod)
                        <x-product-card
                            :prod="$prod"
                            :gold-prices="$goldPrices"
                            :silver-prices="$silverPrices"
                            :added="!empty($justAdded[$prod->id])"
                            :index="$idx"
                            :detailed="true"
                        />
                    @endforeach
                </div>
            @else
                <div class="text-center py-20" data-reveal>
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: #F2EBD9;">
                        <svg class="w-8 h-8" style="color: #A67922;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                        </svg>
                    </div>
                    <p class="text-lg font-medium mb-1" style="color: #5C5341;">No products in this category yet</p>
                    <p class="text-sm mb-5" style="color: #A39A85;">Try another category or browse everything we offer.</p>
                    <button wire:click="setCategory('all')" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_22px_rgba(13,61,31,0.3)]" style="background: linear-gradient(135deg, #0D3D1F, #165A42); color: #F4E2A0;">
                        Show All Products
                    </button>
                </div>
            @endif
        </div>
    </section>
</div>
