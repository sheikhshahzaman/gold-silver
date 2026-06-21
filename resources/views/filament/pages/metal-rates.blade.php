<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-filament::button type="submit">
                Save Rates
            </x-filament::button>

            <x-filament::button type="button" color="gray" icon="heroicon-o-arrow-down-tray" wire:click="pullLiveRates">
                Pull current live rates
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
