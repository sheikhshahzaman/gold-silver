<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap gap-3">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>

            <x-filament::button type="button" color="gray" wire:click="sendTest">
                Send Test Webhook
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
