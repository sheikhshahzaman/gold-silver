<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'site_name' => 'PakGold Rates',
            'contact_phone' => '+92-300-0000000',
            'contact_whatsapp' => '+92-300-0000000',
            'contact_email' => 'info@pakgold.com',
            'contact_address' => 'Islamabad, Pakistan',
            'hours_mon_thu' => '10AM - 8PM',
            'hours_fri' => '3PM - 9:30PM',
            'hours_sat' => '12PM - 9:30PM',
            'hours_sun' => '2PM - 9:30PM',
            'bank_name' => 'Bank Name',
            'bank_account_title' => 'Account Title',
            'bank_account_number' => '0000000000',
            'bank_iban' => 'PK00BANK0000000000000',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
