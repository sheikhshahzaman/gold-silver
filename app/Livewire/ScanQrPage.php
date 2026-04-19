<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Scan QR Code - Islamabad Bullion Exchange')]
class ScanQrPage extends Component
{
    public function render()
    {
        return view('livewire.scan-qr-page');
    }
}
