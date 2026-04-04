<div class="max-w-2xl mx-auto px-4 py-6"
     x-data="{
        priceBasis: 'sell',
        nisabBasis: 'silver',
        goldEnabled: true,
        silverEnabled: true,
        goldEntries: [{ category: '24k', unit: 'gram', amount: '' }],
        silverEntries: [{ unit: 'gram', amount: '' }],
        goldPrices: @js($goldPrices),
        silverPricePerGram: @js($silverPricePerGram),

        // Conversion constants
        tolaInGrams: 11.6638,

        // Nisab thresholds in grams
        goldNisabGrams: 87.48,
        silverNisabGrams: 612.36,

        // Format number as PKR with commas
        formatPkr(value) {
            if (!value || isNaN(value) || value === 0) return 'Rs 0.00';
            return 'Rs ' + Number(value).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        // Get gold price per gram for a category based on selected price basis
        getGoldPricePerGram(category) {
            const cat = category.toLowerCase();
            const priceData = this.goldPrices[cat];
            if (!priceData) return 0;
            return Number(priceData[this.priceBasis]) || 0;
        },

        // Get silver price per gram based on selected price basis
        getSilverPricePerGram() {
            return Number(this.silverPricePerGram[this.priceBasis]) || 0;
        },

        // Get unit multiplier (converts amount in unit to grams)
        getUnitMultiplier(unit) {
            switch(unit) {
                case 'tola': return this.tolaInGrams;
                case 'kg': return 1000;
                default: return 1; // gram
            }
        },

        // Calculate value for a single gold entry
        calcGoldEntryValue(entry) {
            const amount = Number(entry.amount) || 0;
            if (amount === 0) return 0;
            const pricePerGram = this.getGoldPricePerGram(entry.category);
            const multiplier = this.getUnitMultiplier(entry.unit);
            return amount * multiplier * pricePerGram;
        },

        // Calculate value for a single silver entry
        calcSilverEntryValue(entry) {
            const amount = Number(entry.amount) || 0;
            if (amount === 0) return 0;
            const pricePerGram = this.getSilverPricePerGram();
            const multiplier = this.getUnitMultiplier(entry.unit);
            return amount * multiplier * pricePerGram;
        },

        // Convert a gold entry amount to grams (for nisab check)
        goldEntryToGrams(entry) {
            const amount = Number(entry.amount) || 0;
            return amount * this.getUnitMultiplier(entry.unit);
        },

        // Convert a silver entry amount to grams (for nisab check)
        silverEntryToGrams(entry) {
            const amount = Number(entry.amount) || 0;
            return amount * this.getUnitMultiplier(entry.unit);
        },

        // Total gold value in PKR
        get goldTotal() {
            if (!this.goldEnabled) return 0;
            return this.goldEntries.reduce((sum, entry) => sum + this.calcGoldEntryValue(entry), 0);
        },

        // Total silver value in PKR
        get silverTotal() {
            if (!this.silverEnabled) return 0;
            return this.silverEntries.reduce((sum, entry) => sum + this.calcSilverEntryValue(entry), 0);
        },

        // Combined total
        get totalValue() {
            return this.goldTotal + this.silverTotal;
        },

        // Zakat amount (2.5%)
        get zakatAmount() {
            return this.totalValue * 0.025;
        },

        // Nisab threshold value in PKR
        get nisabThresholdValue() {
            if (this.nisabBasis === 'gold') {
                return this.goldNisabGrams * this.getGoldPricePerGram('24k');
            }
            return this.silverNisabGrams * this.getSilverPricePerGram();
        },

        // Whether user meets nisab
        get meetsNisab() {
            return this.totalValue >= this.nisabThresholdValue && this.totalValue > 0;
        },

        // Add a new gold entry
        addGoldEntry() {
            this.goldEntries.push({ category: '24k', unit: 'gram', amount: '' });
        },

        // Remove a gold entry by index
        removeGoldEntry(index) {
            if (this.goldEntries.length > 1) {
                this.goldEntries.splice(index, 1);
            }
        },

        // Add a new silver entry
        addSilverEntry() {
            this.silverEntries.push({ unit: 'gram', amount: '' });
        },

        // Remove a silver entry by index
        removeSilverEntry(index) {
            if (this.silverEntries.length > 1) {
                this.silverEntries.splice(index, 1);
            }
        }
     }"
>
    {{-- ============================================= --}}
    {{-- HEADER                                        --}}
    {{-- ============================================= --}}
    <div class="mb-6">
        {{-- Top row: back arrow + title --}}
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('more') }}" class="hover:text-gold transition-colors" style="color: #888;">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gold">Zakat Calculator</h1>
                <p class="text-sm" style="color: #888;">Live Zakat Estimator</p>
            </div>
        </div>

        {{-- Badges row --}}
        <div class="flex flex-wrap items-center gap-2 mt-3">
            @if($lastUpdated)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs rounded-full border" style="background: #0F3D2E; color: #ccc; border-color: rgba(198,150,60,0.2);">
                    <span class="live-dot"></span>
                    Updated (PKT): {{ \Carbon\Carbon::parse($lastUpdated)->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                </span>
            @endif
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gold/15 text-gold border border-gold/20">
                PKR Rs
            </span>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- SECTION 1: SETTINGS                           --}}
    {{-- ============================================= --}}
    <div class="glass-card p-5 mb-4">
        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            Settings
        </h2>

        <div class="space-y-4">
            {{-- Price Basis --}}
            <div>
                <label class="block text-sm text-gray-500 mb-1.5">Price Basis</label>
                <select x-model="priceBasis"
                        class="w-full border rounded-lg text-sm" style="background: #F7F2EA; border-color: #E8DFD0; color: #0A2E23; rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold/50 transition-colors">
                    <option value="buy">Buy Price</option>
                    <option value="sell">Sell Price</option>
                </select>
            </div>

            {{-- Nisab Basis --}}
            <div>
                <label class="block text-sm text-gray-500 mb-1.5">Nisab Basis</label>
                <select x-model="nisabBasis"
                        class="w-full border rounded-lg text-sm" style="background: #F7F2EA; border-color: #E8DFD0; color: #0A2E23; rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold/50 transition-colors">
                    <option value="silver">Silver Nisab (612.36g)</option>
                    <option value="gold">Gold Nisab (87.48g)</option>
                </select>
            </div>

            {{-- Tip --}}
            <div class="flex items-start gap-2 bg-cream-dark rounded-lg px-3 py-2.5 border border-gold/10">
                <svg class="w-4 h-4 text-gold mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <p class="text-xs text-gray-400">Silver Nisab is commonly used because it is more inclusive, ensuring more people fulfill their Zakat obligation.</p>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- SECTION 2: WHAT YOU OWN (TOGGLES)             --}}
    {{-- ============================================= --}}
    <div class="glass-card p-5 mb-4">
        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
            </svg>
            What You Own
        </h2>

        <div class="space-y-4">
            {{-- Gold Toggle --}}
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gold">Gold</span>
                    <p class="text-xs text-gray-400">Add multiple categories (24K/22K/...)</p>
                </div>
                <button type="button"
                        @click="goldEnabled = !goldEnabled"
                        :class="goldEnabled ? 'bg-gold' : 'bg-emerald-700'"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                    <span :class="goldEnabled ? 'translate-x-5' : 'translate-x-0'"
                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>

            {{-- Silver Toggle --}}
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium" style="color: #0A2E23;">Silver</span>
                    <p class="text-xs text-gray-400">Add your silver weight</p>
                </div>
                <button type="button"
                        @click="silverEnabled = !silverEnabled"
                        :class="silverEnabled ? 'bg-gold' : 'bg-emerald-700'"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                    <span :class="silverEnabled ? 'translate-x-5' : 'translate-x-0'"
                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- SECTION 3: GOLD ENTRIES                       --}}
    {{-- ============================================= --}}
    <div x-show="goldEnabled" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="glass-card p-5 mb-4">
        {{-- Header with Add button --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
                </svg>
                Gold Entries
            </h2>
            <button @click="addGoldEntry()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gold border border-gold/30 rounded-lg hover:bg-gold/10 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add
            </button>
        </div>

        {{-- Gold entry rows --}}
        <div class="space-y-4">
            <template x-for="(entry, index) in goldEntries" :key="index">
                <div class="bg-cream-dark rounded-lg p-4 border border-gold/10">
                    {{-- Entry label + remove --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gold" x-text="'Entry ' + (index + 1)"></span>
                        <button x-show="goldEntries.length > 1"
                                @click="removeGoldEntry(index)"
                                class="text-gray-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Category + Unit dropdowns --}}
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Category</label>
                            <select x-model="entry.category"
                                    class="w-full border" style="background: #F7F2EA; border-color: #E8DFD0; color: #0A2E23; rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:border-gold/50 transition-colors">
                                <option value="spot">Spot</option>
                                <option value="24k">24K</option>
                                <option value="22k">22K</option>
                                <option value="21k">21K</option>
                                <option value="18k">18K</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Unit</label>
                            <select x-model="entry.unit"
                                    class="w-full border" style="background: #F7F2EA; border-color: #E8DFD0; color: #0A2E23; rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:border-gold/50 transition-colors">
                                <option value="gram">Gram</option>
                                <option value="tola">Tola</option>
                                <option value="kg">KG</option>
                            </select>
                        </div>
                    </div>

                    {{-- Amount input --}}
                    <div class="mb-3">
                        <label class="block text-xs text-gray-400 mb-1">Amount</label>
                        <input type="number"
                               x-model.number="entry.amount"
                               placeholder="Your gold amount"
                               min="0"
                               step="any"
                               class="w-full border" style="background: #F7F2EA; border-color: #E8DFD0; color: #0A2E23; rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold/50 transition-colors [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    </div>

                    {{-- Estimated value --}}
                    <div class="flex items-center justify-between bg-cream-dark rounded-lg px-3 py-2">
                        <span class="text-xs text-gray-400">Estimated Value</span>
                        <span class="text-sm font-semibold text-green-400" x-text="formatPkr(calcGoldEntryValue(entry))"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- SECTION 4: SILVER ENTRIES                     --}}
    {{-- ============================================= --}}
    <div x-show="silverEnabled" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="glass-card p-5 mb-4">
        {{-- Header with Add button --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
                </svg>
                Silver Entries
            </h2>
            <button @click="addSilverEntry()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-600 border" style="border-color: #E8DFD0; rounded-lg hover:bg-white/5 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add
            </button>
        </div>

        {{-- Silver entry rows --}}
        <div class="space-y-4">
            <template x-for="(entry, index) in silverEntries" :key="index">
                <div class="bg-cream-dark rounded-lg p-4 border" style="border-color: #E8DFD0;">
                    {{-- Entry label + remove --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-700" x-text="'Entry ' + (index + 1)"></span>
                        <button x-show="silverEntries.length > 1"
                                @click="removeSilverEntry(index)"
                                class="text-gray-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Unit dropdown --}}
                    <div class="mb-3">
                        <label class="block text-xs text-gray-400 mb-1">Unit</label>
                        <select x-model="entry.unit"
                                class="w-full border" style="background: #F7F2EA; border-color: #E8DFD0; color: #0A2E23; rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:border-gold/50 transition-colors">
                            <option value="gram">Gram</option>
                            <option value="tola">Tola</option>
                            <option value="kg">KG</option>
                        </select>
                    </div>

                    {{-- Amount input --}}
                    <div class="mb-3">
                        <label class="block text-xs text-gray-400 mb-1">Amount</label>
                        <input type="number"
                               x-model.number="entry.amount"
                               placeholder="Your silver amount"
                               min="0"
                               step="any"
                               class="w-full border" style="background: #F7F2EA; border-color: #E8DFD0; color: #0A2E23; rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-gold/50 transition-colors [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    </div>

                    {{-- Estimated value --}}
                    <div class="flex items-center justify-between bg-cream-dark rounded-lg px-3 py-2">
                        <span class="text-xs text-gray-400">Estimated Value</span>
                        <span class="text-sm font-semibold text-green-400" x-text="formatPkr(calcSilverEntryValue(entry))"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- SECTION 5: ESTIMATED TOTAL                    --}}
    {{-- ============================================= --}}
    <div class="glass-card p-5 mb-4" id="zakat-result">
        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>
            Estimated Total
        </h2>

        <div class="space-y-3">
            {{-- Gold Value --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Gold Value</span>
                <span class="text-sm font-medium" style="color: #0A2E23;" x-text="formatPkr(goldTotal)"></span>
            </div>

            {{-- Silver Value --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Silver Value</span>
                <span class="text-sm font-medium" style="color: #0A2E23;" x-text="formatPkr(silverTotal)"></span>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gold/15"></div>

            {{-- Total --}}
            <div class="flex items-center justify-between">
                <span class="text-base font-semibold" style="color: #0A2E23;">Total</span>
                <span class="text-lg font-bold" style="color: #0A2E23;" x-text="formatPkr(totalValue)"></span>
            </div>

            {{-- Zakat Amount --}}
            <div class="flex items-center justify-between bg-gold/10 rounded-lg px-4 py-3 border border-gold/20">
                <span class="text-base font-semibold text-gold">Zakat (2.5%)</span>
                <span class="text-xl font-bold text-gold" x-text="formatPkr(zakatAmount)"></span>
            </div>

            {{-- Nisab threshold message --}}
            <div class="mt-2">
                <template x-if="totalValue > 0 && meetsNisab">
                    <div class="flex items-center gap-2 bg-green-500/10 rounded-lg px-3 py-2.5 border border-green-500/20">
                        <svg class="w-5 h-5 text-green-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-400">You meet the Nisab threshold</p>
                            <p class="text-xs text-gray-400" x-text="'Nisab (' + (nisabBasis === 'gold' ? 'Gold: 87.48g' : 'Silver: 612.36g') + '): ' + formatPkr(nisabThresholdValue)"></p>
                        </div>
                    </div>
                </template>
                <template x-if="totalValue > 0 && !meetsNisab">
                    <div class="flex items-center gap-2 bg-amber-500/10 rounded-lg px-3 py-2.5 border border-amber-500/20">
                        <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-amber-400">Below Nisab threshold</p>
                            <p class="text-xs text-gray-400" x-text="'Nisab (' + (nisabBasis === 'gold' ? 'Gold: 87.48g' : 'Silver: 612.36g') + '): ' + formatPkr(nisabThresholdValue)"></p>
                        </div>
                    </div>
                </template>
                <template x-if="totalValue === 0">
                    <div class="flex items-center gap-2 bg-cream-dark rounded-lg px-3 py-2.5 border" style="border-color: #E8DFD0;">
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <p class="text-sm text-gray-400">Enter your gold or silver amounts above to calculate Zakat</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- BOTTOM BUTTONS                                --}}
    {{-- ============================================= --}}
    <div class="flex gap-3 mb-6">
        <a href="{{ route('more') }}"
           class="flex-1 inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-gray-600 rounded-lg border" style="border-color: #E8DFD0; hover:bg-white/5 transition-colors">
            Cancel
        </a>
        <button @click="document.getElementById('zakat-result').scrollIntoView({ behavior: 'smooth' })"
                class="flex-1 btn-gold text-sm">
            View Result
        </button>
    </div>
</div>
