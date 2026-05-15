@php($aria_hidden = $aria_hidden ?? false)
<div class="review-card group relative rounded-xl p-5 mb-4 transition-all duration-300"
     @if($aria_hidden) aria-hidden="true" @endif
     style="background: white; border: 1px solid #E5DCC8; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    {{-- subtle gold quote mark --}}
    <svg aria-hidden="true" class="absolute top-3 right-3 w-6 h-6 opacity-15" fill="currentColor" viewBox="0 0 24 24" style="color: #C9A84C;">
        <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
    </svg>

    {{-- stars --}}
    <div class="flex items-center gap-0.5 mb-3" aria-label="{{ $review->stars }} out of 5 stars">
        @for($i = 1; $i <= 5; $i++)
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" style="color: {{ $i <= $review->stars ? '#C9A84C' : '#E5DCC8' }};">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118L10.49 15.347a1 1 0 00-1.176 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L1.957 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/>
            </svg>
        @endfor
    </div>

    {{-- text --}}
    <p class="text-[13px] leading-relaxed mb-4" style="color: #4B4B4B;">
        &ldquo;{{ $review->text }}&rdquo;
    </p>

    {{-- author --}}
    <div class="flex items-center gap-2.5 pt-3" style="border-top: 1px solid #F0E8DB;">
        <span class="w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-semibold flex-shrink-0"
              style="background: linear-gradient(135deg, #C9A84C, #E8C96A); color: #0A2E23;">
            {{ $review->initials ?: strtoupper(substr($review->name, 0, 2)) }}
        </span>
        <div class="min-w-0">
            <div class="text-[13px] font-semibold truncate" style="color: #1A1A1A;">{{ $review->name }}</div>
            @if($review->location)
                <div class="text-[11px]" style="color: #8B6914;">{{ $review->location }}</div>
            @endif
        </div>
    </div>
</div>
