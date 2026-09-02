<?php

namespace App\Filament\Resources\StaffAuditLogResource\Pages;

use App\Filament\Resources\StaffAuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListStaffAuditLogs extends ListRecords
{
    protected static string $resource = StaffAuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
