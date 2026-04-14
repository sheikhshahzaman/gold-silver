<div>
    {{-- Cream background wrapper --}}
    <div style="background: #FAF6EE; min-height: 100vh; padding-bottom: 3rem;">

        {{-- Dark green header banner --}}
        <div style="background: linear-gradient(135deg, #0A2E23 0%, #0F3D2E 50%, #0A2E23 100%); padding: 2rem 1rem 2.5rem; margin-bottom: 0; position: relative; overflow: hidden;">
            {{-- Decorative gold shimmer line --}}
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, #C9A84C, transparent);"></div>
            {{-- Subtle pattern overlay --}}
            <div style="position: absolute; inset: 0; background: radial-gradient(circle at 20% 50%, rgba(201,168,76,0.06) 0%, transparent 50%), radial-gradient(circle at 80% 50%, rgba(201,168,76,0.04) 0%, transparent 50%);"></div>

            <div style="max-width: 42rem; margin: 0 auto; position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        {{-- Sell icon --}}
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #C9A84C, #B8933E); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(201,168,76,0.3);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 style="font-size: 1.5rem; font-weight: 800; color: #FFFFFF; margin: 0; letter-spacing: -0.02em;">Sell Gold & Silver</h1>
                            <p style="font-size: 0.75rem; color: rgba(201,168,76,0.7); margin: 0; margin-top: 2px; font-weight: 500;">Get the best sell rates instantly</p>
                        </div>
                    </div>
                    {{-- Live badge --}}
                    <div style="display: flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 999px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.25); backdrop-filter: blur(10px);">
                        <span class="live-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #10B981; display: inline-block; animation: livePulse 2s ease-in-out infinite;"></span>
                        <span style="color: #10B981; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em;">LIVE</span>
                    </div>
                </div>

                {{-- Updated timestamp + PKR badge --}}
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.75rem;">
                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.5); margin: 0;">
                        Sell - Updated (PKT):
                        @if($lastUpdated)
                            {{ \Carbon\Carbon::parse($lastUpdated)->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                        @else
                            --
                        @endif
                    </p>
                    <span style="display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; font-size: 0.65rem; font-weight: 700; background: rgba(201,168,76,0.15); color: #C9A84C; border: 1px solid rgba(201,168,76,0.25); letter-spacing: 0.05em;">PKR</span>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div style="max-width: 42rem; margin: 0 auto; padding: 0 1rem;">

            {{-- Step Wizard Indicator --}}
            <div style="display: flex; align-items: center; justify-content: center; margin: 2rem 0 2.5rem;">
                <div style="display: flex; align-items: center; gap: 0;">
                    {{-- Step 1 circle --}}
                    <div style="width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; transition: all 0.3s ease;
                        {{ $step >= 1 ? 'background: linear-gradient(135deg, #C9A84C, #B8933E); color: #fff; box-shadow: 0 4px 15px rgba(201,168,76,0.35);' : 'background: #E8DFD0; color: #999;' }}">
                        @if($step > 1)
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        @else
                            1
                        @endif
                    </div>
                    {{-- Connector bar --}}
                    <div style="width: 80px; height: 3px; border-radius: 2px; transition: all 0.3s ease;
                        {{ $step >= 2 ? 'background: linear-gradient(90deg, #C9A84C, #B8933E);' : 'background: #E8DFD0;' }}"></div>
                    {{-- Step 2 circle --}}
                    <div style="width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; transition: all 0.3s ease;
                        {{ $step >= 2 ? 'background: linear-gradient(135deg, #C9A84C, #B8933E); color: #fff; box-shadow: 0 4px 15px rgba(201,168,76,0.35);' : 'background: #E8DFD0; color: #999;' }}">
                        2
                    </div>
                </div>
            </div>

            {{-- Step 1: Choose Metal --}}
            @if($step === 1)
                <div style="animation: fadeInUp 0.4s ease-out;">
                    {{-- Step heading --}}
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <h2 style="font-size: 1.35rem; font-weight: 700; color: #0A2E23; margin: 0 0 0.35rem;">Step 1: Choose Metal</h2>
                        <p style="font-size: 0.85rem; color: #999; margin: 0;">Select the metal you want to sell</p>
                    </div>

                    {{-- Metal selection cards --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.75rem;">
                        {{-- Gold card --}}
                        <button wire:click="selectMetal('gold')"
                            style="background: #FFFFFF; border-radius: 16px; padding: 1.75rem 1rem; text-align: center; cursor: pointer; transition: all 0.3s ease; border: 2px solid {{ $selectedMetal === 'gold' ? '#C9A84C' : '#EDE6D6' }}; box-shadow: {{ $selectedMetal === 'gold' ? '0 8px 30px rgba(201,168,76,0.2)' : '0 2px 10px rgba(0,0,0,0.04)' }}; position: relative; overflow: hidden;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='{{ $selectedMetal === 'gold' ? '0 8px 30px rgba(201,168,76,0.2)' : '0 2px 10px rgba(0,0,0,0.04)' }}';">
                            @if($selectedMetal === 'gold')
                                <div style="position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; border-radius: 50%; background: linear-gradient(135deg, #C9A84C, #B8933E); display: flex; align-items: center; justify-content: center;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            @endif
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: {{ $selectedMetal === 'gold' ? 'linear-gradient(135deg, rgba(201,168,76,0.2), rgba(201,168,76,0.08))' : '#F7F2EA' }}; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="{{ $selectedMetal === 'gold' ? '#C9A84C' : '#BBBBBB' }}" style="transition: all 0.3s ease;">
                                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                                    </svg>
                                </div>
                                <span style="font-size: 1.05rem; font-weight: 700; color: {{ $selectedMetal === 'gold' ? '#C9A84C' : '#555' }}; transition: all 0.3s ease;">Gold</span>
                            </div>
                        </button>

                        {{-- Silver card --}}
                        <button wire:click="selectMetal('silver')"
                            style="background: #FFFFFF; border-radius: 16px; padding: 1.75rem 1rem; text-align: center; cursor: pointer; transition: all 0.3s ease; border: 2px solid {{ $selectedMetal === 'silver' ? '#C9A84C' : '#EDE6D6' }}; box-shadow: {{ $selectedMetal === 'silver' ? '0 8px 30px rgba(201,168,76,0.2)' : '0 2px 10px rgba(0,0,0,0.04)' }}; position: relative; overflow: hidden;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='{{ $selectedMetal === 'silver' ? '0 8px 30px rgba(201,168,76,0.2)' : '0 2px 10px rgba(0,0,0,0.04)' }}';">
                            @if($selectedMetal === 'silver')
                                <div style="position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; border-radius: 50%; background: linear-gradient(135deg, #C9A84C, #B8933E); display: flex; align-items: center; justify-content: center;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            @endif
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: {{ $selectedMetal === 'silver' ? 'linear-gradient(135deg, rgba(201,168,76,0.2), rgba(201,168,76,0.08))' : '#F7F2EA' }}; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="{{ $selectedMetal === 'silver' ? '#C9A84C' : '#BBBBBB' }}" stroke-width="1.5" style="transition: all 0.3s ease;">
                                        <circle cx="12" cy="12" r="10"/>
                                    </svg>
                                </div>
                                <span style="font-size: 1.05rem; font-weight: 700; color: {{ $selectedMetal === 'silver' ? '#C9A84C' : '#555' }}; transition: all 0.3s ease;">Silver</span>
                            </div>
                        </button>
                    </div>

                    {{-- Karat Selection (Gold only) --}}
                    @if($selectedMetal === 'gold')
                        <div style="background: #FFFFFF; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1.75rem; border: 1px solid #EDE6D6; box-shadow: 0 2px 10px rgba(0,0,0,0.03); animation: fadeInUp 0.3s ease-out;">
                            <label style="font-size: 0.8rem; font-weight: 600; color: #0A2E23; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.75rem;">Select Karat</label>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                @foreach(['24k' => '24K', 'rawa' => 'Rawa', '22k' => '22K', '21k' => '21K', '18k' => '18K'] as $key => $label)
                                    <button wire:click="selectKarat('{{ $key }}')"
                                        style="padding: 0.5rem 1.15rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600; transition: all 0.2s ease; cursor: pointer; border: none;
                                            {{ $selectedKarat === $key
                                                ? 'background: linear-gradient(135deg, #C9A84C, #B8933E); color: #fff; box-shadow: 0 4px 12px rgba(201,168,76,0.3);'
                                                : 'background: #F0E8DB; color: #555;' }}"
                                        onmouseover="@if($selectedKarat !== $key) this.style.background='#E8DFD0'; @endif"
                                        onmouseout="@if($selectedKarat !== $key) this.style.background='#F0E8DB'; @endif">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Continue button --}}
                    <button wire:click="nextStep"
                        style="width: 100%; padding: 1rem; border-radius: 14px; font-size: 1rem; font-weight: 700; color: #fff; background: linear-gradient(135deg, #C9A84C 0%, #B8933E 50%, #C9A84C 100%); background-size: 200% 100%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 6px 20px rgba(201,168,76,0.35); transition: all 0.3s ease; letter-spacing: 0.02em;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(201,168,76,0.45)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(201,168,76,0.35)';">
                        Continue
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>

            @else
                {{-- Step 2: Quantity & Price --}}
                <div style="animation: fadeInUp 0.4s ease-out;">
                    {{-- Step heading --}}
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <h2 style="font-size: 1.35rem; font-weight: 700; color: #0A2E23; margin: 0 0 0.35rem;">Step 2: Quantity</h2>
                        <p style="font-size: 0.85rem; color: #999; margin: 0;">See SELL price instantly</p>
                    </div>

                    {{-- Selected metal badge --}}
                    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 1rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600; background: rgba(201,168,76,0.12); color: #A67922; border: 1px solid rgba(201,168,76,0.25);">
                            @if($selectedMetal === 'gold')
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                                Gold {{ strtoupper($selectedKarat) }}
                            @else
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/></svg>
                                Silver
                            @endif
                        </span>
                    </div>

                    {{-- Unit selection --}}
                    <div style="background: #FFFFFF; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; border: 1px solid #EDE6D6; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #0A2E23; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.75rem;">Select Unit</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            @foreach(['tola' => 'Tola', 'gram' => 'Gram', '10_gram' => '10 Gram', 'kg' => 'KG'] as $key => $label)
                                <button wire:click="selectUnit('{{ $key }}')"
                                    style="padding: 0.5rem 1.15rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600; transition: all 0.2s ease; cursor: pointer; border: none;
                                        {{ $selectedUnit === $key
                                            ? 'background: linear-gradient(135deg, #C9A84C, #B8933E); color: #fff; box-shadow: 0 4px 12px rgba(201,168,76,0.3);'
                                            : 'background: #F0E8DB; color: #555;' }}"
                                    onmouseover="@if($selectedUnit !== $key) this.style.background='#E8DFD0'; @endif"
                                    onmouseout="@if($selectedUnit !== $key) this.style.background='#F0E8DB'; @endif">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Quantity controls --}}
                    <div style="background: #FFFFFF; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; border: 1px solid #EDE6D6; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #0A2E23; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.75rem;">Quantity</label>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            {{-- Minus button --}}
                            <button wire:click="decrementQuantity"
                                style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid #EDE6D6; background: #FFFFFF; color: #0A2E23; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; flex-shrink: 0;"
                                onmouseover="this.style.borderColor='#C9A84C'; this.style.background='rgba(201,168,76,0.08)';"
                                onmouseout="this.style.borderColor='#EDE6D6'; this.style.background='#FFFFFF';">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                            </button>
                            {{-- Quantity input --}}
                            <input type="number" wire:model.live.debounce.300ms="quantity" min="1" max="9999"
                                style="flex: 1; border-radius: 14px; padding: 0.75rem 1rem; text-align: center; font-size: 1.35rem; font-weight: 800; color: #0A2E23; background: #F7F2EA; border: 2px solid #EDE6D6; outline: none; transition: all 0.2s ease; -moz-appearance: textfield; -webkit-appearance: none; appearance: textfield;"
                                onfocus="this.style.borderColor='#C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';"
                                onblur="this.style.borderColor='#EDE6D6'; this.style.boxShadow='none';">
                            {{-- Plus button --}}
                            <button wire:click="incrementQuantity"
                                style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid #EDE6D6; background: #FFFFFF; color: #0A2E23; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; flex-shrink: 0;"
                                onmouseover="this.style.borderColor='#C9A84C'; this.style.background='rgba(201,168,76,0.08)';"
                                onmouseout="this.style.borderColor='#EDE6D6'; this.style.background='#FFFFFF';">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Total Sell Price card (dark green with gold text) --}}
                    <div style="background: linear-gradient(135deg, #0A2E23 0%, #0F3D2E 100%); border-radius: 20px; padding: 2rem 1.5rem; text-align: center; margin-bottom: 1.5rem; position: relative; overflow: hidden; box-shadow: 0 10px 40px rgba(10,46,35,0.25);">
                        {{-- Decorative elements --}}
                        <div style="position: absolute; top: -30px; right: -30px; width: 100px; height: 100px; border-radius: 50%; background: rgba(201,168,76,0.06);"></div>
                        <div style="position: absolute; bottom: -20px; left: -20px; width: 80px; height: 80px; border-radius: 50%; background: rgba(201,168,76,0.04);"></div>
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(201,168,76,0.3), transparent);"></div>

                        <div style="position: relative; z-index: 1;">
                            <p style="font-size: 0.8rem; color: rgba(201,168,76,0.7); margin: 0 0 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;">Total Sell Price</p>
                            @if($calculatedPrice !== null)
                                <p style="font-size: 2.25rem; font-weight: 800; color: #C9A84C; margin: 0; letter-spacing: -0.02em; text-shadow: 0 2px 10px rgba(201,168,76,0.2);">
                                    Rs {{ number_format($calculatedPrice, 0) }}
                                </p>
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.75rem;">
                                    <div style="width: 4px; height: 4px; border-radius: 50%; background: rgba(201,168,76,0.4);"></div>
                                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.45); margin: 0;">
                                        {{ $quantity }} x @if($selectedMetal === 'gold') Gold {{ strtoupper($selectedKarat) }} @else Silver @endif
                                        ({{ str_replace('_', ' ', ucfirst($selectedUnit)) }})
                                    </p>
                                    <div style="width: 4px; height: 4px; border-radius: 50%; background: rgba(201,168,76,0.4);"></div>
                                </div>
                            @else
                                <p style="font-size: 1.5rem; font-weight: 700; color: rgba(255,255,255,0.25); margin: 0;">Price unavailable</p>
                            @endif
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div style="display: flex; gap: 0.75rem;">
                        {{-- Back button --}}
                        <button wire:click="prevStep"
                            style="padding: 0 1.35rem; height: 52px; border-radius: 14px; font-weight: 600; background: #FFFFFF; color: #0A2E23; border: 2px solid #EDE6D6; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                            onmouseover="this.style.borderColor='#C9A84C'; this.style.background='rgba(201,168,76,0.05)';"
                            onmouseout="this.style.borderColor='#EDE6D6'; this.style.background='#FFFFFF';">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        </button>
                        {{-- Place Sell Order button --}}
                        <button wire:click="placeOrder"
                            @if($calculatedPrice === null) disabled @endif
                            style="flex: 1; height: 52px; border-radius: 14px; font-size: 1rem; font-weight: 700; color: #fff; border: none; cursor: {{ $calculatedPrice === null ? 'not-allowed' : 'pointer' }}; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.3s ease; letter-spacing: 0.02em;
                                {{ $calculatedPrice === null
                                    ? 'background: #CCCCCC; box-shadow: none; opacity: 0.6;'
                                    : 'background: linear-gradient(135deg, #C9A84C 0%, #B8933E 50%, #C9A84C 100%); background-size: 200% 100%; box-shadow: 0 6px 20px rgba(201,168,76,0.35);' }}"
                            @if($calculatedPrice !== null)
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(201,168,76,0.45)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(201,168,76,0.35)';"
                            @endif>
                            Place Sell Order
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Animations --}}
    <style>
        @keyframes livePulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Hide number input spinners across browsers */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</div>
