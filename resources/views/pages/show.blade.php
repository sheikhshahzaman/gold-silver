<x-layouts.app :title="$page->title . ' - Islamabad Bullion Exchange'">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="glass-card p-8 md:p-12">
            <h1 class="text-3xl md:text-4xl font-bold mb-8" style="color: #0A2E23;">{{ $page->title }}</h1>

            <div class="prose max-w-none
                        prose-headings:font-semibold
                        prose-a:text-gold prose-a:hover:text-gold-dark"
                 style="color: #333;">
                {!! $page->body !!}
            </div>
        </div>
    </div>
</x-layouts.app>
