<x-filament-panels::page>
    {{-- Logo Section --}}
    <x-filament::section>
        <x-slot name="heading">Logo</x-slot>
        <x-slot name="description">Upload your site logo. Recommended: PNG or JPG, max 1MB.</x-slot>

        @php $currentLogo = \App\Models\Setting::get('site_logo'); @endphp

        {{-- Current Logo Preview --}}
        @if($currentLogo && Storage::disk('public')->exists($currentLogo))
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-400 mb-2">Current Logo</p>
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-lg border border-gray-700 bg-gray-800">
                        <img src="{{ Storage::disk('public')->url($currentLogo) }}" alt="Current logo" class="h-12 w-auto object-contain">
                    </div>
                    <x-filament::button color="danger" size="sm" wire:click="removeLogo">
                        Remove Logo
                    </x-filament::button>
                </div>
            </div>
        @endif

        {{-- Upload New Logo --}}
        <div>
            <p class="text-sm font-medium text-gray-400 mb-2">{{ $currentLogo ? 'Replace Logo' : 'Upload Logo' }}</p>
            <div class="flex items-center gap-3">
                <div>
                    @if($logoUpload)
                        <div class="mb-2 p-2 rounded-lg border border-gray-700 bg-gray-800 inline-block">
                            <img src="{{ $logoUpload->temporaryUrl() }}" alt="Preview" class="h-12 w-auto object-contain">
                        </div>
                    @endif
                    <input type="file" wire:model="logoUpload" accept="image/*"
                        class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-500 cursor-pointer">
                    @error('logoUpload') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <div wire:loading wire:target="logoUpload" class="text-sm text-amber-400 mt-1">Uploading...</div>
                </div>

                @if($logoUpload)
                    <x-filament::button wire:click="saveLogo" size="sm">
                        Save Logo
                    </x-filament::button>
                @endif
            </div>
        </div>
    </x-filament::section>

    {{-- Site Identity Form --}}
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
