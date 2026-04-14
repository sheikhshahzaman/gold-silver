<div style="background-color: #FAF6EE; min-height: 100vh;"
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
    {{-- HERO BANNER                                   --}}
    {{-- ============================================= --}}
    <div style="background: linear-gradient(135deg, #0A2E23 0%, #0F3D2E 50%, #0A2E23 100%); position: relative; overflow: hidden;">
        {{-- Decorative pattern overlay --}}
        <div style="position: absolute; inset: 0; opacity: 0.04; background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cpath d=&quot;M30 0L60 30L30 60L0 30Z&quot; fill=&quot;%23C9A84C&quot; fill-opacity=&quot;0.5&quot;/%3E%3C/svg%3E'); background-size: 30px 30px;"></div>

        <div class="max-w-2xl mx-auto px-5 pt-6 pb-8" style="position: relative; z-index: 1;">
            {{-- Back arrow --}}
            <a href="{{ route('more') }}" class="inline-flex items-center gap-2 mb-5 transition-opacity hover:opacity-80" style="color: #C9A84C;">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span class="text-sm font-medium">Back</span>
            </a>

            {{-- Title block --}}
            <div class="mb-4">
                <h1 class="text-3xl font-bold tracking-tight mb-1" style="color: #C9A84C;">Zakat Calculator</h1>
                <p class="text-sm font-medium" style="color: rgba(201, 168, 76, 0.6);">Live Zakat Estimator</p>
            </div>

            {{-- Badges row --}}
            <div class="flex flex-wrap items-center gap-2">
                @if($lastUpdated)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-full" style="background: rgba(201, 168, 76, 0.1); color: rgba(255,255,255,0.7); border: 1px solid rgba(201, 168, 76, 0.2);">
                        <span class="live-dot"></span>
                        {{ \Carbon\Carbon::parse($lastUpdated)->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                    </span>
                @endif
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full" style="background: rgba(201, 168, 76, 0.15); color: #C9A84C; border: 1px solid rgba(201, 168, 76, 0.3);">
                    PKR Rs
                </span>
            </div>
        </div>

        {{-- Bottom gold accent line --}}
        <div style="height: 3px; background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>
    </div>

    {{-- ============================================= --}}
    {{-- MAIN CONTENT                                  --}}
    {{-- ============================================= --}}
    <div class="max-w-2xl mx-auto px-4 py-6 space-y-5">

        {{-- ============================================= --}}
        {{-- SECTION 1: SETTINGS                           --}}
        {{-- ============================================= --}}
        <div style="background: #FFFFFF; border: 1px solid #E8DFD0; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(10, 46, 35, 0.04);">
            {{-- Card header with gold accent --}}
            <div style="border-bottom: 1px solid #E8DFD0; padding: 16px 20px; background: linear-gradient(135deg, rgba(201, 168, 76, 0.04), transparent);">
                <h2 class="text-base font-semibold flex items-center gap-2.5" style="color: #0A2E23;">
                    <span class="flex items-center justify-center" style="width: 32px; height: 32px; background: rgba(201, 168, 76, 0.12); border-radius: 8px;">
                        <svg class="w-4.5 h-4.5" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span>
                    Settings
                </h2>
            </div>

            <div style="padding: 20px;" class="space-y-5">
                {{-- Price Basis --}}
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #0A2E23;">Price Basis</label>
                    <div style="position: relative;">
                        <select x-model="priceBasis"
                                style="width: 100%; background: #FAF6EE; border: 1.5px solid #E8DFD0; color: #0A2E23; border-radius: 10px; padding: 10px 14px; font-size: 14px; outline: none; appearance: none; cursor: pointer; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='#E8DFD0'">
                            <option value="buy">Buy Price</option>
                            <option value="sell">Sell Price</option>
                        </select>
                        <div style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                            <svg class="w-4 h-4" style="color: #999;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </div>
                    </div>
                </div>

                {{-- Nisab Basis --}}
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #0A2E23;">Nisab Basis</label>
                    <div style="position: relative;">
                        <select x-model="nisabBasis"
                                style="width: 100%; background: #FAF6EE; border: 1.5px solid #E8DFD0; color: #0A2E23; border-radius: 10px; padding: 10px 14px; font-size: 14px; outline: none; appearance: none; cursor: pointer; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='#E8DFD0'">
                            <option value="silver">Silver Nisab (612.36g)</option>
                            <option value="gold">Gold Nisab (87.48g)</option>
                        </select>
                        <div style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                            <svg class="w-4 h-4" style="color: #999;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </div>
                    </div>
                </div>

                {{-- Tip --}}
                <div class="flex items-start gap-2.5" style="background: rgba(201, 168, 76, 0.06); border-radius: 10px; padding: 12px 14px; border: 1px solid rgba(201, 168, 76, 0.12);">
                    <svg class="w-4 h-4 shrink-0" style="color: #C9A84C; margin-top: 1px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="text-xs leading-relaxed" style="color: #7A7060;">Silver Nisab is commonly used because it is more inclusive, ensuring more people fulfill their Zakat obligation.</p>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- SECTION 2: WHAT YOU OWN (TOGGLES)             --}}
        {{-- ============================================= --}}
        <div style="background: #FFFFFF; border: 1px solid #E8DFD0; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(10, 46, 35, 0.04);">
            {{-- Card header --}}
            <div style="border-bottom: 1px solid #E8DFD0; padding: 16px 20px; background: linear-gradient(135deg, rgba(201, 168, 76, 0.04), transparent);">
                <h2 class="text-base font-semibold flex items-center gap-2.5" style="color: #0A2E23;">
                    <span class="flex items-center justify-center" style="width: 32px; height: 32px; background: rgba(201, 168, 76, 0.12); border-radius: 8px;">
                        <svg class="w-4.5 h-4.5" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                        </svg>
                    </span>
                    What You Own
                </h2>
            </div>

            <div style="padding: 20px;" class="space-y-0">
                {{-- Gold Toggle --}}
                <div class="flex items-center justify-between" style="padding: 14px 0; border-bottom: 1px solid #F0EBE1;">
                    <div>
                        <span class="text-sm font-semibold" style="color: #C9A84C;">Gold</span>
                        <p class="text-xs mt-0.5" style="color: #999;">Add multiple categories (24K / 22K / ...)</p>
                    </div>
                    <button type="button"
                            @click="goldEnabled = !goldEnabled"
                            :class="goldEnabled ? '' : ''"
                            :style="goldEnabled ? 'background-color: #C9A84C' : 'background-color: #D1CBC0'"
                            style="position: relative; display: inline-flex; height: 26px; width: 48px; flex-shrink: 0; cursor: pointer; border-radius: 9999px; border: 2px solid transparent; transition: background-color 0.2s ease-in-out;">
                        <span :style="goldEnabled ? 'transform: translateX(22px)' : 'transform: translateX(0)'"
                              style="pointer-events: none; display: inline-block; height: 22px; width: 22px; border-radius: 9999px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.15); transition: transform 0.2s ease-in-out;"></span>
                    </button>
                </div>

                {{-- Silver Toggle --}}
                <div class="flex items-center justify-between" style="padding: 14px 0;">
                    <div>
                        <span class="text-sm font-semibold" style="color: #6B7280;">Silver</span>
                        <p class="text-xs mt-0.5" style="color: #999;">Add your silver weight</p>
                    </div>
                    <button type="button"
                            @click="silverEnabled = !silverEnabled"
                            :style="silverEnabled ? 'background-color: #C9A84C' : 'background-color: #D1CBC0'"
                            style="position: relative; display: inline-flex; height: 26px; width: 48px; flex-shrink: 0; cursor: pointer; border-radius: 9999px; border: 2px solid transparent; transition: background-color 0.2s ease-in-out;">
                        <span :style="silverEnabled ? 'transform: translateX(22px)' : 'transform: translateX(0)'"
                              style="pointer-events: none; display: inline-block; height: 22px; width: 22px; border-radius: 9999px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.15); transition: transform 0.2s ease-in-out;"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- SECTION 3: GOLD ENTRIES                       --}}
        {{-- ============================================= --}}
        <div x-show="goldEnabled" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             style="background: #FFFFFF; border: 1px solid #E8DFD0; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(10, 46, 35, 0.04);">
            {{-- Card header with Add button --}}
            <div class="flex items-center justify-between" style="border-bottom: 1px solid #E8DFD0; padding: 16px 20px; background: linear-gradient(135deg, rgba(201, 168, 76, 0.06), rgba(201, 168, 76, 0.02));">
                <h2 class="text-base font-semibold flex items-center gap-2.5" style="color: #C9A84C;">
                    <span class="flex items-center justify-center" style="width: 32px; height: 32px; background: rgba(201, 168, 76, 0.15); border-radius: 8px;">
                        <svg class="w-4.5 h-4.5" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
                        </svg>
                    </span>
                    Gold Entries
                </h2>
                <button @click="addGoldEntry()"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; font-size: 12px; font-weight: 600; color: #C9A84C; border: 1.5px solid rgba(201, 168, 76, 0.35); border-radius: 8px; background: rgba(201, 168, 76, 0.06); cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(201, 168, 76, 0.12)'" onmouseout="this.style.background='rgba(201, 168, 76, 0.06)'">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add
                </button>
            </div>

            {{-- Gold entry rows --}}
            <div style="padding: 20px;" class="space-y-4">
                <template x-for="(entry, index) in goldEntries" :key="index">
                    <div style="background: #FAF6EE; border-radius: 12px; padding: 18px; border: 1px solid rgba(201, 168, 76, 0.15);">
                        {{-- Entry label + remove --}}
                        <div class="flex items-center justify-between" style="margin-bottom: 14px;">
                            <span class="text-sm font-semibold" style="color: #C9A84C;" x-text="'Entry ' + (index + 1)"></span>
                            <button x-show="goldEntries.length > 1"
                                    @click="removeGoldEntry(index)"
                                    style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; font-size: 11px; color: #DC2626; border-radius: 6px; background: rgba(220, 38, 38, 0.06); border: 1px solid rgba(220, 38, 38, 0.15); cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='rgba(220, 38, 38, 0.12)'" onmouseout="this.style.background='rgba(220, 38, 38, 0.06)'">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                                Remove
                            </button>
                        </div>

                        {{-- Category + Unit dropdowns --}}
                        <div class="grid grid-cols-2 gap-3" style="margin-bottom: 12px;">
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: #7A7060;">Category</label>
                                <div style="position: relative;">
                                    <select x-model="entry.category"
                                            style="width: 100%; background: #FFFFFF; border: 1.5px solid #E8DFD0; color: #0A2E23; border-radius: 8px; padding: 9px 12px; font-size: 13px; outline: none; appearance: none; cursor: pointer; transition: border-color 0.2s;"
                                            onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='#E8DFD0'">
                                        <option value="spot">Spot</option>
                                        <option value="24k">24K</option>
                                        <option value="22k">22K</option>
                                        <option value="21k">21K</option>
                                        <option value="18k">18K</option>
                                    </select>
                                    <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                        <svg class="w-3.5 h-3.5" style="color: #999;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: #7A7060;">Unit</label>
                                <div style="position: relative;">
                                    <select x-model="entry.unit"
                                            style="width: 100%; background: #FFFFFF; border: 1.5px solid #E8DFD0; color: #0A2E23; border-radius: 8px; padding: 9px 12px; font-size: 13px; outline: none; appearance: none; cursor: pointer; transition: border-color 0.2s;"
                                            onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='#E8DFD0'">
                                        <option value="gram">Gram</option>
                                        <option value="tola">Tola</option>
                                        <option value="kg">KG</option>
                                    </select>
                                    <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                        <svg class="w-3.5 h-3.5" style="color: #999;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Amount input --}}
                        <div style="margin-bottom: 14px;">
                            <label class="block text-xs font-medium mb-1.5" style="color: #7A7060;">Amount</label>
                            <input type="number"
                                   x-model.number="entry.amount"
                                   placeholder="Enter gold amount"
                                   min="0"
                                   step="any"
                                   style="width: 100%; background: #FFFFFF; border: 1.5px solid #E8DFD0; color: #0A2E23; border-radius: 8px; padding: 10px 14px; font-size: 14px; outline: none; transition: border-color 0.2s; -webkit-appearance: textfield; -moz-appearance: textfield;"
                                   class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                   onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='#E8DFD0'">
                        </div>

                        {{-- Estimated value --}}
                        <div class="flex items-center justify-between" style="background: #FFFFFF; border-radius: 8px; padding: 10px 14px; border: 1px solid #E8DFD0;">
                            <span class="text-xs font-medium" style="color: #999;">Estimated Value</span>
                            <span class="text-sm font-bold" style="color: #0A2E23;" x-text="formatPkr(calcGoldEntryValue(entry))"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- SECTION 4: SILVER ENTRIES                     --}}
        {{-- ============================================= --}}
        <div x-show="silverEnabled" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             style="background: #FFFFFF; border: 1px solid #E8DFD0; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(10, 46, 35, 0.04);">
            {{-- Card header with Add button --}}
            <div class="flex items-center justify-between" style="border-bottom: 1px solid #E8DFD0; padding: 16px 20px; background: linear-gradient(135deg, rgba(107, 114, 128, 0.04), transparent);">
                <h2 class="text-base font-semibold flex items-center gap-2.5" style="color: #4B5563;">
                    <span class="flex items-center justify-center" style="width: 32px; height: 32px; background: rgba(107, 114, 128, 0.1); border-radius: 8px;">
                        <svg class="w-4.5 h-4.5" style="color: #6B7280;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
                        </svg>
                    </span>
                    Silver Entries
                </h2>
                <button @click="addSilverEntry()"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; font-size: 12px; font-weight: 600; color: #6B7280; border: 1.5px solid rgba(107, 114, 128, 0.3); border-radius: 8px; background: rgba(107, 114, 128, 0.04); cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(107, 114, 128, 0.1)'" onmouseout="this.style.background='rgba(107, 114, 128, 0.04)'">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add
                </button>
            </div>

            {{-- Silver entry rows --}}
            <div style="padding: 20px;" class="space-y-4">
                <template x-for="(entry, index) in silverEntries" :key="index">
                    <div style="background: #FAF6EE; border-radius: 12px; padding: 18px; border: 1px solid #E8DFD0;">
                        {{-- Entry label + remove --}}
                        <div class="flex items-center justify-between" style="margin-bottom: 14px;">
                            <span class="text-sm font-semibold" style="color: #4B5563;" x-text="'Entry ' + (index + 1)"></span>
                            <button x-show="silverEntries.length > 1"
                                    @click="removeSilverEntry(index)"
                                    style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; font-size: 11px; color: #DC2626; border-radius: 6px; background: rgba(220, 38, 38, 0.06); border: 1px solid rgba(220, 38, 38, 0.15); cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='rgba(220, 38, 38, 0.12)'" onmouseout="this.style.background='rgba(220, 38, 38, 0.06)'">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                                Remove
                            </button>
                        </div>

                        {{-- Unit dropdown --}}
                        <div style="margin-bottom: 12px;">
                            <label class="block text-xs font-medium mb-1.5" style="color: #7A7060;">Unit</label>
                            <div style="position: relative;">
                                <select x-model="entry.unit"
                                        style="width: 100%; background: #FFFFFF; border: 1.5px solid #E8DFD0; color: #0A2E23; border-radius: 8px; padding: 9px 12px; font-size: 13px; outline: none; appearance: none; cursor: pointer; transition: border-color 0.2s;"
                                        onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='#E8DFD0'">
                                    <option value="gram">Gram</option>
                                    <option value="tola">Tola</option>
                                    <option value="kg">KG</option>
                                </select>
                                <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                    <svg class="w-3.5 h-3.5" style="color: #999;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Amount input --}}
                        <div style="margin-bottom: 14px;">
                            <label class="block text-xs font-medium mb-1.5" style="color: #7A7060;">Amount</label>
                            <input type="number"
                                   x-model.number="entry.amount"
                                   placeholder="Enter silver amount"
                                   min="0"
                                   step="any"
                                   style="width: 100%; background: #FFFFFF; border: 1.5px solid #E8DFD0; color: #0A2E23; border-radius: 8px; padding: 10px 14px; font-size: 14px; outline: none; transition: border-color 0.2s; -webkit-appearance: textfield; -moz-appearance: textfield;"
                                   class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                   onfocus="this.style.borderColor='#C9A84C'" onblur="this.style.borderColor='#E8DFD0'">
                        </div>

                        {{-- Estimated value --}}
                        <div class="flex items-center justify-between" style="background: #FFFFFF; border-radius: 8px; padding: 10px 14px; border: 1px solid #E8DFD0;">
                            <span class="text-xs font-medium" style="color: #999;">Estimated Value</span>
                            <span class="text-sm font-bold" style="color: #0A2E23;" x-text="formatPkr(calcSilverEntryValue(entry))"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- SECTION 5: SUMMARY / RESULT                  --}}
        {{-- ============================================= --}}
        <div id="zakat-result" style="background: linear-gradient(135deg, #0A2E23 0%, #0F3D2E 50%, #0A2E23 100%); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(10, 46, 35, 0.2); position: relative;">
            {{-- Decorative pattern --}}
            <div style="position: absolute; inset: 0; opacity: 0.03; background-image: url('data:image/svg+xml,%3Csvg width=&quot;40&quot; height=&quot;40&quot; viewBox=&quot;0 0 40 40&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Ccircle cx=&quot;20&quot; cy=&quot;20&quot; r=&quot;8&quot; fill=&quot;%23C9A84C&quot;/%3E%3C/svg%3E'); background-size: 40px 40px;"></div>

            {{-- Card header --}}
            <div style="padding: 18px 22px; border-bottom: 1px solid rgba(201, 168, 76, 0.15); position: relative; z-index: 1;">
                <h2 class="text-base font-semibold flex items-center gap-2.5" style="color: #C9A84C;">
                    <span class="flex items-center justify-center" style="width: 32px; height: 32px; background: rgba(201, 168, 76, 0.12); border-radius: 8px;">
                        <svg class="w-4.5 h-4.5" style="color: #C9A84C;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </span>
                    Estimated Total
                </h2>
            </div>

            <div style="padding: 22px; position: relative; z-index: 1;" class="space-y-4">
                {{-- Gold Value --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm" style="color: rgba(255,255,255,0.5);">Gold Value</span>
                    <span class="text-sm font-semibold" style="color: rgba(255,255,255,0.85);" x-text="formatPkr(goldTotal)"></span>
                </div>

                {{-- Silver Value --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm" style="color: rgba(255,255,255,0.5);">Silver Value</span>
                    <span class="text-sm font-semibold" style="color: rgba(255,255,255,0.85);" x-text="formatPkr(silverTotal)"></span>
                </div>

                {{-- Divider --}}
                <div style="border-top: 1px solid rgba(201, 168, 76, 0.2);"></div>

                {{-- Total --}}
                <div class="flex items-center justify-between">
                    <span class="text-base font-bold" style="color: #C9A84C;">Total</span>
                    <span class="text-lg font-bold" style="color: #C9A84C;" x-text="formatPkr(totalValue)"></span>
                </div>

                {{-- Zakat Amount - Highlighted --}}
                <div style="background: linear-gradient(135deg, rgba(201, 168, 76, 0.15), rgba(201, 168, 76, 0.08)); border: 1.5px solid rgba(201, 168, 76, 0.3); border-radius: 12px; padding: 16px 18px;">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-medium block" style="color: rgba(201, 168, 76, 0.6); text-transform: uppercase; letter-spacing: 0.05em;">Zakat Due</span>
                            <span class="text-base font-bold" style="color: #C9A84C;">2.5%</span>
                        </div>
                        <span class="text-2xl font-bold" style="color: #C9A84C; text-shadow: 0 0 20px rgba(201, 168, 76, 0.2);" x-text="formatPkr(zakatAmount)"></span>
                    </div>
                </div>

                {{-- Nisab threshold messages --}}
                <div class="mt-1">
                    <template x-if="totalValue > 0 && meetsNisab">
                        <div class="flex items-center gap-3" style="background: rgba(34, 197, 94, 0.1); border-radius: 10px; padding: 12px 16px; border: 1px solid rgba(34, 197, 94, 0.2);">
                            <span class="flex items-center justify-center shrink-0" style="width: 28px; height: 28px; background: rgba(34, 197, 94, 0.15); border-radius: 50%;">
                                <svg class="w-4 h-4" style="color: #22C55E;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold" style="color: #22C55E;">You meet the Nisab threshold</p>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.45);" x-text="'Nisab (' + (nisabBasis === 'gold' ? 'Gold: 87.48g' : 'Silver: 612.36g') + '): ' + formatPkr(nisabThresholdValue)"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="totalValue > 0 && !meetsNisab">
                        <div class="flex items-center gap-3" style="background: rgba(245, 158, 11, 0.1); border-radius: 10px; padding: 12px 16px; border: 1px solid rgba(245, 158, 11, 0.2);">
                            <span class="flex items-center justify-center shrink-0" style="width: 28px; height: 28px; background: rgba(245, 158, 11, 0.15); border-radius: 50%;">
                                <svg class="w-4 h-4" style="color: #F59E0B;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold" style="color: #F59E0B;">Below Nisab threshold</p>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.45);" x-text="'Nisab (' + (nisabBasis === 'gold' ? 'Gold: 87.48g' : 'Silver: 612.36g') + '): ' + formatPkr(nisabThresholdValue)"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="totalValue === 0">
                        <div class="flex items-center gap-3" style="background: rgba(255,255,255,0.05); border-radius: 10px; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.08);">
                            <svg class="w-5 h-5 shrink-0" style="color: rgba(255,255,255,0.3);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">Enter your gold or silver amounts above to calculate Zakat</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- BOTTOM BUTTONS                                --}}
        {{-- ============================================= --}}
        <div class="flex gap-3" style="padding-bottom: 24px;">
            <a href="{{ route('more') }}"
               style="flex: 1; display: inline-flex; align-items: center; justify-content: center; padding: 13px 24px; font-size: 14px; font-weight: 600; color: #6B7280; border-radius: 12px; border: 1.5px solid #E8DFD0; background: #FFFFFF; text-decoration: none; transition: all 0.2s; cursor: pointer;"
               onmouseover="this.style.background='#FAF6EE'; this.style.borderColor='#D1CBC0'" onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#E8DFD0'">
                Cancel
            </a>
            <button @click="document.getElementById('zakat-result').scrollIntoView({ behavior: 'smooth' })"
                    style="flex: 1; display: inline-flex; align-items: center; justify-content: center; padding: 13px 24px; font-size: 14px; font-weight: 600; color: #FFFFFF; border-radius: 12px; border: none; background: linear-gradient(135deg, #C9A84C, #B8963F); cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(201, 168, 76, 0.3);"
                    onmouseover="this.style.boxShadow='0 4px 16px rgba(201, 168, 76, 0.4)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(201, 168, 76, 0.3)'; this.style.transform='translateY(0)'">
                View Result
            </button>
        </div>
    </div>
</div>
