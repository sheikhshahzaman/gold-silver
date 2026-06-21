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
            'site_name' => 'Islamabad Bullion Exchange',
            'contact_phone' => '+92-340-2786222',
            'contact_whatsapp' => '+923409786111',
            'contact_email' => 'thelegacyjewellers@gmail.com',
            'contact_address' => 'Shop No 1, Ground Floor, Trade Center, F-7 Markaz Block 20-B F-7, Islamabad, 44210',
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

        // Gold/silver rate controls. Created only when missing so re-seeding never
        // clobbers values the admin has set in the panel. Defaults are realistic
        // starting points; the admin adjusts them (or presses "Pull live rates").
        $rateDefaults = [
            // 'manual' = gold/silver use admin-entered rates; 'live' = market.
            'rate_mode' => 'manual',
            // Gold base price per TOLA, per karat.
            'manual_gold_24k' => '445500',
            'manual_gold_22k' => '408391',
            'manual_gold_21k' => '389813',
            'manual_gold_18k' => '334125',
            'manual_gold_rawa' => '443273',
            // Silver base price per the named unit.
            'manual_silver_tola' => '7400',
            'manual_silver_10_tola' => '74000',
            'manual_silver_10_gram' => '6345',
            'manual_silver_5_gram' => '3172',
            'manual_silver_gram' => '634',
            'manual_silver_kg' => '634467',
            // Optional silver note shown under the silver table in the app (off by default).
            'silver_note_active' => '0',
            'silver_note_en' => '',
            'silver_note_ur' => '',
        ];

        foreach ($rateDefaults as $key => $value) {
            if (Setting::get($key) === null) {
                Setting::set($key, $value);
            }
        }
    }
}
