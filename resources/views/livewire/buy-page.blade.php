<div>
    {{-- ================================================================ --}}
    {{-- Cream background wrapper                                         --}}
    {{-- ================================================================ --}}
    <div style="background: #FAF6EE; min-height: 100vh;">

        {{-- ============================================================ --}}
        {{-- Dark-green header banner                                     --}}
        {{-- ============================================================ --}}
        <div style="background: linear-gradient(135deg, #0A2E23 0%, #0F3D2E 100%); padding: 1.5rem 0;">
            <div style="max-width: 42rem; margin: 0 auto; padding: 0 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h1 style="color: #FFFFFF; font-size: 1.5rem; font-weight: 700; margin: 0; letter-spacing: -0.025em;">
                            Buy{{ $productName ? ': ' . $productName : '' }}
                        </h1>
                        <p style="color: rgba(255,255,255,0.5); font-size: 0.8rem; margin-top: 0.25rem;">
                            Updated (PKT):
                            @if($lastUpdated)
                                {{ \Carbon\Carbon::parse($lastUpdated)->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                            @else
                                --
                            @endif
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(74,222,128,0.25); padding: 0.35rem 0.85rem; border-radius: 9999px;">
                        <span class="live-dot"></span>
                        <span style="color: #4ADE80; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">LIVE</span>
                    </div>
                </div>
                {{-- PKR currency badge --}}
                <div style="margin-top: 0.5rem;">
                    <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 600; background: rgba(201,168,76,0.15); color: #C9A84C; border: 1px solid rgba(201,168,76,0.3);">
                        PKR
                    </span>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Main content area                                            --}}
        {{-- ============================================================ --}}
        <div style="max-width: 42rem; margin: 0 auto; padding: 1.5rem 1rem 3rem;">

            {{-- ======================================================== --}}
            {{-- Step Wizard Indicator (gold circles + connecting line)    --}}
            {{-- ======================================================== --}}
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 2.5rem;">
                <div style="display: flex; align-items: center; gap: 0;">
                    {{-- Step 1 circle --}}
                    <div style="
                        width: 2.75rem; height: 2.75rem; border-radius: 50%;
                        display: flex; align-items: center; justify-content: center;
                        font-weight: 700; font-size: 0.85rem;
                        transition: all 0.3s ease;
                        {{ $step >= 1
                            ? 'background: linear-gradient(135deg, #C9A84C, #A67922); color: #FFFFFF; box-shadow: 0 4px 12px rgba(201,168,76,0.35);'
                            : 'background: #E8DFD0; color: #999;' }}
                    ">
                        1
                    </div>

                    {{-- Connecting line --}}
                    <div style="
                        width: 5rem; height: 3px; border-radius: 2px;
                        transition: all 0.3s ease;
                        {{ $step >= 2
                            ? 'background: linear-gradient(90deg, #C9A84C, #A67922);'
                            : 'background: #E8DFD0;' }}
                    "></div>

                    {{-- Step 2 circle --}}
                    <div style="
                        width: 2.75rem; height: 2.75rem; border-radius: 50%;
                        display: flex; align-items: center; justify-content: center;
                        font-weight: 700; font-size: 0.85rem;
                        transition: all 0.3s ease;
                        {{ $step >= 2
                            ? 'background: linear-gradient(135deg, #C9A84C, #A67922); color: #FFFFFF; box-shadow: 0 4px 12px rgba(201,168,76,0.35);'
                            : 'background: #E8DFD0; color: #999;' }}
                    ">
                        2
                    </div>
                </div>
            </div>

            {{-- Step labels --}}
            <div style="display: flex; justify-content: center; gap: 4.5rem; margin-top: -1.75rem; margin-bottom: 2rem;">
                <span style="font-size: 0.7rem; font-weight: 600; color: {{ $step >= 1 ? '#C9A84C' : '#999' }}; text-transform: uppercase; letter-spacing: 0.05em;">Metal</span>
                <span style="font-size: 0.7rem; font-weight: 600; color: {{ $step >= 2 ? '#C9A84C' : '#999' }}; text-transform: uppercase; letter-spacing: 0.05em;">Quantity</span>
            </div>


            {{-- ======================================================== --}}
            {{-- STEP 1: Choose Metal & Karat                             --}}
            {{-- ======================================================== --}}
            @if($step === 1)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                    {{-- Section title --}}
                    <div style="text-align: center; margin-bottom: 0.5rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #0A2E23; margin: 0 0 0.25rem;">Choose Your Metal</h2>
                        <p style="font-size: 0.85rem; color: #6B6B6B; margin: 0;">Select Gold or Silver to continue</p>
                    </div>

                    {{-- Metal selection cards (2-col grid) --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">

                        {{-- Gold card --}}
                        <button wire:click="selectMetal('gold')"
                            style="
                                position: relative; padding: 1.75rem 1rem; text-align: center;
                                border-radius: 1rem; cursor: pointer;
                                transition: all 0.3s ease;
                                border: 2px solid {{ $selectedMetal === 'gold' ? 'transparent' : '#E8DFD0' }};
                                background: {{ $selectedMetal === 'gold' ? '#FFFDF7' : '#FFFFFF' }};
                                box-shadow: {{ $selectedMetal === 'gold' ? '0 8px 24px rgba(201,168,76,0.2)' : '0 2px 8px rgba(0,0,0,0.04)' }};
                                {{ $selectedMetal === 'gold' ? 'background-image: linear-gradient(#FFFDF7,#FFFDF7),linear-gradient(135deg,#C9A84C,#A67922); background-origin: border-box; background-clip: padding-box,border-box; border-color: transparent;' : '' }}
                            "
                            onmouseenter="if('{{ $selectedMetal }}' !== 'gold') { this.style.borderColor='#C9A84C'; this.style.boxShadow='0 6px 20px rgba(201,168,76,0.15)'; this.style.transform='translateY(-2px)'; }"
                            onmouseleave="if('{{ $selectedMetal }}' !== 'gold') { this.style.borderColor='#E8DFD0'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'; }"
                        >
                            {{-- Checkmark badge --}}
                            @if($selectedMetal === 'gold')
                                <div style="position: absolute; top: 0.6rem; right: 0.6rem; width: 1.4rem; height: 1.4rem; border-radius: 50%; background: linear-gradient(135deg, #C9A84C, #A67922); display: flex; align-items: center; justify-content: center;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                </div>
                            @endif
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                                <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: {{ $selectedMetal === 'gold' ? 'linear-gradient(135deg, #C9A84C, #A67922)' : '#F0E8DB' }}; transition: all 0.3s ease;">
                                    <svg style="width: 1.5rem; height: 1.5rem; color: {{ $selectedMetal === 'gold' ? '#FFFFFF' : '#999' }};" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                                    </svg>
                                </div>
                                <span style="font-size: 1.05rem; font-weight: 700; color: {{ $selectedMetal === 'gold' ? '#C9A84C' : '#555' }}; transition: color 0.3s ease;">Gold</span>
                            </div>
                        </button>

                        {{-- Silver card --}}
                        <button wire:click="selectMetal('silver')"
                            style="
                                position: relative; padding: 1.75rem 1rem; text-align: center;
                                border-radius: 1rem; cursor: pointer;
                                transition: all 0.3s ease;
                                border: 2px solid {{ $selectedMetal === 'silver' ? 'transparent' : '#E8DFD0' }};
                                background: {{ $selectedMetal === 'silver' ? '#FAFAFA' : '#FFFFFF' }};
                                box-shadow: {{ $selectedMetal === 'silver' ? '0 8px 24px rgba(201,168,76,0.2)' : '0 2px 8px rgba(0,0,0,0.04)' }};
                                {{ $selectedMetal === 'silver' ? 'background-image: linear-gradient(#FAFAFA,#FAFAFA),linear-gradient(135deg,#C9A84C,#A67922); background-origin: border-box; background-clip: padding-box,border-box; border-color: transparent;' : '' }}
                            "
                            onmouseenter="if('{{ $selectedMetal }}' !== 'silver') { this.style.borderColor='#C9A84C'; this.style.boxShadow='0 6px 20px rgba(201,168,76,0.15)'; this.style.transform='translateY(-2px)'; }"
                            onmouseleave="if('{{ $selectedMetal }}' !== 'silver') { this.style.borderColor='#E8DFD0'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'; }"
                        >
                            @if($selectedMetal === 'silver')
                                <div style="position: absolute; top: 0.6rem; right: 0.6rem; width: 1.4rem; height: 1.4rem; border-radius: 50%; background: linear-gradient(135deg, #C9A84C, #A67922); display: flex; align-items: center; justify-content: center;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                </div>
                            @endif
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                                <div style="width: 3.5rem; height: 3.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: {{ $selectedMetal === 'silver' ? 'linear-gradient(135deg, #B0BEC5, #78909C)' : '#F0E8DB' }}; transition: all 0.3s ease;">
                                    <svg style="width: 1.5rem; height: 1.5rem; color: {{ $selectedMetal === 'silver' ? '#FFFFFF' : '#999' }};" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="10"/>
                                    </svg>
                                </div>
                                <span style="font-size: 1.05rem; font-weight: 700; color: {{ $selectedMetal === 'silver' ? '#78909C' : '#555' }}; transition: color 0.3s ease;">Silver</span>
                            </div>
                        </button>
                    </div>

                    {{-- Karat selection (only for gold) --}}
                    @if($selectedMetal === 'gold')
                        <div style="background: #FFFFFF; border-radius: 1rem; padding: 1.25rem 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #E8DFD0;">
                            <label style="font-size: 0.8rem; font-weight: 600; color: #0A2E23; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.75rem;">Select Karat</label>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                @foreach(['24k' => '24K', 'rawa' => 'Rawa', '22k' => '22K', '21k' => '21K', '18k' => '18K'] as $key => $label)
                                    <button wire:click="selectKarat('{{ $key }}')"
                                        style="
                                            padding: 0.5rem 1.15rem; border-radius: 9999px;
                                            font-size: 0.85rem; font-weight: 600;
                                            cursor: pointer; border: none;
                                            transition: all 0.3s ease;
                                            {{ $selectedKarat === $key
                                                ? 'background: linear-gradient(135deg, #C9A84C, #A67922); color: #FFFFFF; box-shadow: 0 3px 10px rgba(201,168,76,0.3);'
                                                : 'background: #F0E8DB; color: #6B6B6B;' }}
                                        "
                                        onmouseenter="if('{{ $selectedKarat }}' !== '{{ $key }}') { this.style.background='#E8DFD0'; this.style.color='#0A2E23'; }"
                                        onmouseleave="if('{{ $selectedKarat }}' !== '{{ $key }}') { this.style.background='#F0E8DB'; this.style.color='#6B6B6B'; }"
                                    >
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Continue button (gold gradient) --}}
                    <button wire:click="nextStep"
                        style="
                            width: 100%; padding: 0.95rem 1.5rem;
                            border-radius: 0.75rem; border: none; cursor: pointer;
                            background: linear-gradient(135deg, #C9A84C, #A67922);
                            color: #FFFFFF; font-size: 1rem; font-weight: 700;
                            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
                            box-shadow: 0 4px 14px rgba(201,168,76,0.35);
                            transition: all 0.3s ease;
                            margin-top: 0.5rem;
                        "
                        onmouseenter="this.style.boxShadow='0 6px 20px rgba(201,168,76,0.45)'; this.style.transform='translateY(-1px)';"
                        onmouseleave="this.style.boxShadow='0 4px 14px rgba(201,168,76,0.35)'; this.style.transform='translateY(0)';"
                    >
                        Continue
                        <svg style="width: 1.15rem; height: 1.15rem;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </button>
                </div>


            {{-- ======================================================== --}}
            {{-- STEP 2: Unit, Quantity, Price & Place Order               --}}
            {{-- ======================================================== --}}
            @else
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                    {{-- Section title --}}
                    <div style="text-align: center; margin-bottom: 0.25rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #0A2E23; margin: 0 0 0.35rem;">Choose Quantity</h2>
                        <p style="font-size: 0.85rem; color: #6B6B6B; margin: 0 0 0.75rem;">See your BUY price instantly</p>

                        {{-- Selected metal badge --}}
                        <div style="display: flex; align-items: center; justify-content: center;">
                            <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; background: rgba(201,168,76,0.12); color: #A67922; border: 1px solid rgba(201,168,76,0.25);">
                                @if($selectedMetal === 'gold')
                                    <svg style="width: 0.9rem; height: 0.9rem;" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                                    </svg>
                                    Gold {{ strtoupper($selectedKarat) }}
                                @else
                                    <svg style="width: 0.9rem; height: 0.9rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="10"/>
                                    </svg>
                                    Silver
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Unit selection card --}}
                    <div style="background: #FFFFFF; border-radius: 1rem; padding: 1.25rem 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #E8DFD0;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #0A2E23; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.75rem;">Select Unit</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            @foreach(['tola' => 'Tola', 'gram' => 'Gram', '10_gram' => '10 Gram', 'kg' => 'KG'] as $key => $label)
                                <button wire:click="selectUnit('{{ $key }}')"
                                    style="
                                        padding: 0.5rem 1.15rem; border-radius: 9999px;
                                        font-size: 0.85rem; font-weight: 600;
                                        cursor: pointer; border: none;
                                        transition: all 0.3s ease;
                                        {{ $selectedUnit === $key
                                            ? 'background: linear-gradient(135deg, #C9A84C, #A67922); color: #FFFFFF; box-shadow: 0 3px 10px rgba(201,168,76,0.3);'
                                            : 'background: #F0E8DB; color: #6B6B6B;' }}
                                    "
                                    onmouseenter="if('{{ $selectedUnit }}' !== '{{ $key }}') { this.style.background='#E8DFD0'; this.style.color='#0A2E23'; }"
                                    onmouseleave="if('{{ $selectedUnit }}' !== '{{ $key }}') { this.style.background='#F0E8DB'; this.style.color='#6B6B6B'; }"
                                >
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Quantity input card --}}
                    <div style="background: #FFFFFF; border-radius: 1rem; padding: 1.25rem 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #E8DFD0;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #0A2E23; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 0.75rem;">Quantity</label>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            {{-- Decrement circle --}}
                            <button wire:click="decrementQuantity"
                                style="
                                    width: 3rem; height: 3rem; border-radius: 50%;
                                    border: 2px solid #E8DFD0; background: #FFFFFF;
                                    display: flex; align-items: center; justify-content: center;
                                    cursor: pointer; transition: all 0.3s ease;
                                    color: #0A2E23; flex-shrink: 0;
                                "
                                onmouseenter="this.style.borderColor='#C9A84C'; this.style.background='#FFFDF7'; this.style.color='#C9A84C';"
                                onmouseleave="this.style.borderColor='#E8DFD0'; this.style.background='#FFFFFF'; this.style.color='#0A2E23';"
                            >
                                <svg style="width: 1.15rem; height: 1.15rem;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                </svg>
                            </button>

                            {{-- Quantity input --}}
                            <input type="number" wire:model.live.debounce.300ms="quantity"
                                min="1" max="9999"
                                style="
                                    flex: 1; border-radius: 0.75rem; padding: 0.75rem 1rem;
                                    text-align: center; font-size: 1.35rem; font-weight: 700;
                                    background: #F7F2EA; border: 2px solid #E8DFD0;
                                    color: #0A2E23; outline: none;
                                    transition: all 0.3s ease;
                                    -moz-appearance: textfield;
                                "
                                class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                onfocus="this.style.borderColor='#C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';"
                                onblur="this.style.borderColor='#E8DFD0'; this.style.boxShadow='none';"
                            >

                            {{-- Increment circle --}}
                            <button wire:click="incrementQuantity"
                                style="
                                    width: 3rem; height: 3rem; border-radius: 50%;
                                    border: 2px solid #E8DFD0; background: #FFFFFF;
                                    display: flex; align-items: center; justify-content: center;
                                    cursor: pointer; transition: all 0.3s ease;
                                    color: #0A2E23; flex-shrink: 0;
                                "
                                onmouseenter="this.style.borderColor='#C9A84C'; this.style.background='#FFFDF7'; this.style.color='#C9A84C';"
                                onmouseleave="this.style.borderColor='#E8DFD0'; this.style.background='#FFFFFF'; this.style.color='#0A2E23';"
                            >
                                <svg style="width: 1.15rem; height: 1.15rem;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Price display card (dark green bg) --}}
                    <div style="
                        background: linear-gradient(135deg, #0A2E23, #0F3D2E);
                        border-radius: 1rem; padding: 1.75rem 1.5rem;
                        text-align: center;
                        box-shadow: 0 8px 24px rgba(10,46,35,0.25);
                        position: relative; overflow: hidden;
                    ">
                        {{-- Decorative gold accent line --}}
                        <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 4rem; height: 3px; background: linear-gradient(90deg, transparent, #C9A84C, transparent); border-radius: 0 0 2px 2px;"></div>

                        <p style="font-size: 0.8rem; color: rgba(255,255,255,0.5); margin: 0 0 0.5rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em;">Total Buy Price</p>

                        @if($calculatedPrice !== null)
                            <p style="font-size: 2.25rem; font-weight: 800; color: #C9A84C; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                                Rs {{ number_format($calculatedPrice, 0) }}
                            </p>
                            <p style="font-size: 0.75rem; color: rgba(255,255,255,0.4); margin: 0.5rem 0 0;">
                                {{ $quantity }} x
                                @if($selectedMetal === 'gold') Gold {{ strtoupper($selectedKarat) }} @else Silver @endif
                                ({{ str_replace('_', ' ', ucfirst($selectedUnit)) }})
                            </p>
                        @else
                            <p style="font-size: 1.5rem; font-weight: 700; color: rgba(255,255,255,0.2); margin: 0;">Price unavailable</p>
                        @endif
                    </div>

                    {{-- Action buttons --}}
                    <div style="display: flex; gap: 0.75rem;">
                        {{-- Back button (outlined) --}}
                        <button wire:click="prevStep"
                            style="
                                padding: 0.95rem 1.25rem; border-radius: 0.75rem;
                                border: 2px solid #E8DFD0; background: #FFFFFF;
                                cursor: pointer; display: flex; align-items: center; justify-content: center;
                                transition: all 0.3s ease; color: #0A2E23;
                            "
                            onmouseenter="this.style.borderColor='#C9A84C'; this.style.color='#C9A84C';"
                            onmouseleave="this.style.borderColor='#E8DFD0'; this.style.color='#0A2E23';"
                        >
                            <svg style="width: 1.15rem; height: 1.15rem;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                            </svg>
                        </button>

                        {{-- Place Order button (gold gradient) --}}
                        <button wire:click="placeOrder"
                            @if($calculatedPrice === null) disabled @endif
                            style="
                                flex: 1; padding: 0.95rem 1.5rem;
                                border-radius: 0.75rem; border: none; cursor: pointer;
                                background: linear-gradient(135deg, #C9A84C, #A67922);
                                color: #FFFFFF; font-size: 1rem; font-weight: 700;
                                display: flex; align-items: center; justify-content: center; gap: 0.5rem;
                                box-shadow: 0 4px 14px rgba(201,168,76,0.35);
                                transition: all 0.3s ease;
                                {{ $calculatedPrice === null ? 'opacity: 0.45; cursor: not-allowed; box-shadow: none;' : '' }}
                            "
                            @if($calculatedPrice !== null)
                                onmouseenter="this.style.boxShadow='0 6px 20px rgba(201,168,76,0.45)'; this.style.transform='translateY(-1px)';"
                                onmouseleave="this.style.boxShadow='0 4px 14px rgba(201,168,76,0.35)'; this.style.transform='translateY(0)';"
                            @endif
                        >
                            Place Order
                            <svg style="width: 1.15rem; height: 1.15rem;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
