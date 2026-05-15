<div>
<section class="relative py-16 px-4 sm:px-6 lg:px-8 overflow-hidden" style="background: linear-gradient(180deg, #F2EBD9 0%, #EDE3CB 100%);">
    {{-- decorative gold haze --}}
    <div aria-hidden="true" class="pointer-events-none absolute -top-32 -left-20 w-[480px] h-[480px] rounded-full opacity-20" style="background: radial-gradient(circle, #C9A84C 0%, transparent 60%);"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -bottom-32 -right-20 w-[480px] h-[480px] rounded-full opacity-20" style="background: radial-gradient(circle, #C9A84C 0%, transparent 60%);"></div>

    <div class="relative max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 text-[11px] tracking-[0.2em] uppercase mb-3" style="color: #8B6914;">
                <span class="w-6 h-px" style="background: #C9A84C;"></span>
                Customer Reviews
                <span class="w-6 h-px" style="background: #C9A84C;"></span>
            </div>
            <h2 class="text-3xl sm:text-4xl tracking-tight mb-2" style="color: #1A1A1A; font-weight: 500;">
                What Our Clients <em class="not-italic" style="color: #8B6914;">Say</em>
            </h2>
            <p class="text-sm" style="color: #6B6B6B;">Real reviews from real customers across Islamabad and beyond.</p>
        </div>

        {{-- ============================================================ --}}
        {{-- Marquee Stage                                                 --}}
        {{-- ============================================================ --}}
        @if($total > 0)
            <div class="reviews-marquee relative">
                {{-- top/bottom fade masks --}}
                <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-16 z-10" style="background: linear-gradient(180deg, #F2EBD9 0%, transparent 100%);"></div>
                <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 h-16 z-10" style="background: linear-gradient(0deg, #EDE3CB 0%, transparent 100%);"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 h-[520px] md:h-[560px] overflow-hidden">

                    {{-- Column A: scrolls UP --}}
                    <div class="reviews-track-wrap">
                        <div class="reviews-track reviews-track--up">
                            @foreach($columnA as $review)
                                @include('livewire.partials.review-card', ['review' => $review])
                            @endforeach
                            {{-- duplicate for seamless loop --}}
                            @foreach($columnA as $review)
                                @include('livewire.partials.review-card', ['review' => $review, 'aria_hidden' => true])
                            @endforeach
                        </div>
                    </div>

                    {{-- Column B: scrolls DOWN --}}
                    <div class="reviews-track-wrap hidden md:block">
                        <div class="reviews-track reviews-track--down">
                            @foreach($columnB as $review)
                                @include('livewire.partials.review-card', ['review' => $review])
                            @endforeach
                            @foreach($columnB as $review)
                                @include('livewire.partials.review-card', ['review' => $review, 'aria_hidden' => true])
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        @else
            <div class="text-center py-10 text-sm" style="color: #6B6B6B;">
                Be the first to share your experience.
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- Share Your Experience CTA                                    --}}
        {{-- ============================================================ --}}
        <div class="mt-10 flex flex-col items-center gap-3">
            <button type="button" wire:click="openForm"
                    class="group inline-flex items-center gap-2.5 px-6 py-3 rounded-full text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #0F3D2E, #0A2E23); color: #E8C96A;">
                <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 14.25v4.5A2.25 2.25 0 0117.25 21H5.25A2.25 2.25 0 013 18.75V6.75A2.25 2.25 0 015.25 4.5h4.5"/>
                </svg>
                Share Your Experience
            </button>
            <p class="text-[11px]" style="color: #8B6914;">Reviews are published after a quick check.</p>
        </div>

    </div>

    {{-- ================================================================ --}}
    {{-- Submit Review Modal                                              --}}
    {{-- ================================================================ --}}
    @if($formOpen)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" wire:key="review-modal" role="dialog" aria-modal="true">
            <div class="absolute inset-0" style="background: rgba(10,46,35,0.55); backdrop-filter: blur(4px);" wire:click="closeForm"></div>

            <div class="relative w-full max-w-md rounded-2xl shadow-2xl overflow-hidden"
                 style="background: #FFFCF5; border: 1px solid #E5DCC8;"
                 x-data x-transition.scale.origin.center>
                <button type="button" wire:click="closeForm" aria-label="Close"
                        class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center text-sm transition-colors hover:bg-black/5" style="color: #6B6B6B;">
                    ✕
                </button>

                @if($submitted)
                    <div class="p-8 text-center">
                        <div class="mx-auto mb-4 w-14 h-14 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #C9A84C, #E8C96A);">
                            <svg class="w-7 h-7 text-emerald-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-1" style="color: #0A2E23;">Thank you!</h3>
                        <p class="text-sm mb-5" style="color: #6B6B6B;">Your review has been received and will be visible once approved.</p>
                        <button type="button" wire:click="closeForm" class="px-5 py-2 rounded-full text-sm font-medium" style="background: #0A2E23; color: #E8C96A;">Close</button>
                    </div>
                @else
                    <form wire:submit.prevent="submit" class="p-6">
                        <div class="mb-5">
                            <div class="text-[11px] tracking-widest uppercase mb-1" style="color: #8B6914;">Share Your Experience</div>
                            <h3 class="text-xl" style="color: #0A2E23; font-weight: 500;">Leave a Review</h3>
                        </div>

                        {{-- Stars (interactive) --}}
                        <div class="mb-4">
                            <label class="block text-xs font-medium mb-2" style="color: #4B4B4B;">Your Rating</label>
                            <div class="flex items-center gap-1" role="radiogroup" aria-label="Rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" wire:click="$set('stars', {{ $i }})"
                                            class="p-1 transition-transform hover:scale-110 focus:outline-none"
                                            aria-label="{{ $i }} stars"
                                            style="color: {{ $i <= $stars ? '#C9A84C' : '#D8C9A2' }};">
                                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10.49 15.347a1 1 0 00-1.176 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L1.957 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/>
                                        </svg>
                                    </button>
                                @endfor
                                <span class="ml-2 text-xs" style="color: #6B6B6B;">{{ $stars }} / 5</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label for="rev-name" class="block text-xs font-medium mb-1" style="color: #4B4B4B;">Your Name *</label>
                                <input id="rev-name" type="text" wire:model="name" maxlength="80"
                                       class="w-full px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-gold/30"
                                       style="background: white; border-color: #E5DCC8; color: #1A1A1A;" placeholder="Ahmed Khan">
                                @error('name') <p class="text-[11px] mt-1" style="color: #B71C1C;">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="rev-loc" class="block text-xs font-medium mb-1" style="color: #4B4B4B;">Location</label>
                                <input id="rev-loc" type="text" wire:model="location" maxlength="80"
                                       class="w-full px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-gold/30"
                                       style="background: white; border-color: #E5DCC8; color: #1A1A1A;" placeholder="F-7, Islamabad">
                                @error('location') <p class="text-[11px] mt-1" style="color: #B71C1C;">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="rev-text" class="block text-xs font-medium mb-1" style="color: #4B4B4B;">Your Review *</label>
                            <textarea id="rev-text" wire:model="text" rows="4" maxlength="600"
                                      class="w-full px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-gold/30"
                                      style="background: white; border-color: #E5DCC8; color: #1A1A1A;" placeholder="Tell us about your experience…"></textarea>
                            @error('text') <p class="text-[11px] mt-1" style="color: #B71C1C;">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <button type="button" wire:click="closeForm" class="px-4 py-2 rounded-full text-sm" style="color: #6B6B6B;">Cancel</button>
                            <button type="submit"
                                    class="px-5 py-2 rounded-full text-sm font-medium transition-all hover:shadow-md disabled:opacity-60"
                                    style="background: linear-gradient(135deg, #0F3D2E, #0A2E23); color: #E8C96A;"
                                    wire:loading.attr="disabled" wire:target="submit">
                                <span wire:loading.remove wire:target="submit">Submit Review</span>
                                <span wire:loading wire:target="submit">Submitting…</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</section>
</div>
