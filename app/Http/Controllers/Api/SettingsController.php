<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * App-wide configuration the mobile app reads at startup so that
     * admin edits (Filament → Site Settings) flow through automatically.
     * Mirrors the same Setting:: keys the website uses.
     */
    public function appConfig(): JsonResponse
    {
        // Settings rarely change; cache for 60s so a flood of app launches
        // doesn't hammer the DB while still picking up admin edits quickly.
        $config = Cache::remember('api.app_config', 60, function () {
            return [
                'site_name' => Setting::get('site_name', 'Islamabad Bullion Exchange'),
                'live_rates_enabled' => Setting::get('live_rates_enabled', '1') === '1',
                'contact' => [
                    'phone' => Setting::get('contact_phone', ''),
                    'whatsapp' => Setting::get('contact_whatsapp', ''),
                    'email' => Setting::get('contact_email', ''),
                    'address' => Setting::get('contact_address', ''),
                    'map_embed_url' => Setting::get('map_embed_url', ''),
                ],
                'hours' => [
                    'mon_thu' => Setting::get('hours_mon_thu', ''),
                    'fri' => Setting::get('hours_fri', ''),
                    'sat' => Setting::get('hours_sat', ''),
                    'sun' => Setting::get('hours_sun', ''),
                ],
            ];
        });

        return response()->json($config, 200, ['Cache-Control' => 'no-store']);
    }
}
