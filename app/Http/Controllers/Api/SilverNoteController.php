<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SilverNoteController extends Controller
{
    /**
     * Optional admin-managed note shown under the silver table in the app.
     * Hidden (is_active=false) unless the admin enables it in Site Settings.
     */
    public function show(): JsonResponse
    {
        $data = Cache::remember('api.silver_note', 60, function () {
            $row = Setting::where('key', 'silver_note_en')->first();

            return [
                'success' => true,
                'note_en' => (string) Setting::get('silver_note_en', ''),
                'note_ur' => (string) Setting::get('silver_note_ur', ''),
                'is_active' => Setting::get('silver_note_active', '0') === '1',
                'updated_at' => $row && $row->updated_at ? $row->updated_at->toIso8601String() : null,
            ];
        });

        return response()->json($data, 200, ['Cache-Control' => 'no-store']);
    }
}
