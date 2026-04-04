<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use App\Models\Contact;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Auto-mark as read when viewing
        if (! $this->record->is_read) {
            $this->record->update(['is_read' => true]);
        }

        return $data;
    }
}
