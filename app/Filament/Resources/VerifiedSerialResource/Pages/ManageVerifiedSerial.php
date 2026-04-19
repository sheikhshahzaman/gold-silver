<?php

namespace App\Filament\Resources\VerifiedSerialResource\Pages;

use App\Filament\Resources\VerifiedSerialResource;
use App\Models\VerifiedSerial;
use Filament\Resources\Pages\EditRecord;

class ManageVerifiedSerial extends EditRecord
{
    protected static string $resource = VerifiedSerialResource::class;

    protected static ?string $title = 'Verification Serial';

    public function mount(int | string $record = null): void
    {
        $singleton = VerifiedSerial::first() ?? VerifiedSerial::create([
            'serial_number' => 'IBE-MASTER-' . now()->year,
            'is_active' => false,
            'label' => 'Default — please update',
        ]);

        parent::mount($singleton->getKey());
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
