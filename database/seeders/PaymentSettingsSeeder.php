<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class PaymentSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'payment_easypaisa_number' => '03001234567',
            'payment_easypaisa_name' => 'PakGold Rates',
            'payment_jazzcash_number' => '03001234567',
            'payment_jazzcash_name' => 'PakGold Rates',
            'payment_raast_id' => '03001234567',
            'payment_raast_name' => 'PakGold Rates',
            'payment_bank_name' => 'Bank Name',
            'payment_bank_account_title' => 'PakGold Rates',
            'payment_bank_account_number' => '0000000000000',
            'payment_bank_iban' => 'PK00BANK0000000000000000',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
