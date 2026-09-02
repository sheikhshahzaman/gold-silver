<?php

namespace App\Filament\Resources\StaffAccountResource\Pages;

use App\Filament\Resources\StaffAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffAccount extends EditRecord
{
    protected static string $resource = StaffAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['role'] = 'staff';
        $data['staff_permissions'] = array_values($data['staff_permissions'] ?? []);

        return $data;
    }
}
