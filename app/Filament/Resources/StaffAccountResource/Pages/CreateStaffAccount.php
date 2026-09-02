<?php

namespace App\Filament\Resources\StaffAccountResource\Pages;

use App\Filament\Resources\StaffAccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffAccount extends CreateRecord
{
    protected static string $resource = StaffAccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'staff';
        $data['staff_permissions'] = array_values($data['staff_permissions'] ?? []);

        return $data;
    }
}
