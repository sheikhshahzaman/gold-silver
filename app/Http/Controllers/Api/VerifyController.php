<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\SerialVerificationAttempt;
use App\Models\VerifiedSerial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    /**
     * Serial-number verification for the app — mirrors the website's
     * VerifySerialPage: inventory items first, then the pre-approved
     * serial list, with every attempt audited.
     */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'serial' => 'required|string|min:5|max:80',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
        ]);

        $normalized = strtoupper(trim($data['serial']));

        // Stored serials carry the IBE- prefix; accept entries without it too.
        $candidates = array_unique([
            $normalized,
            str_starts_with($normalized, 'IBE-') ? $normalized : 'IBE-' . $normalized,
        ]);

        $item = InventoryItem::with('product.productCategory')
            ->whereIn('serial_number', $candidates)
            ->first();

        $approvedSerial = null;
        if (!$item) {
            $approvedSerial = VerifiedSerial::whereIn('serial_number', $candidates)
                ->where('is_active', true)
                ->first();
        }

        SerialVerificationAttempt::create([
            'entered_serial' => $normalized,
            'inventory_item_id' => $item?->id,
            'found' => (bool) ($item || $approvedSerial),
            'status_at_lookup' => $item?->status ?? ($approvedSerial ? 'approved' : null),
            'customer_name' => $data['customer_name'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'attempted_at' => now(),
        ]);

        if ($item) {
            $item->logScan($request->ip(), $request->userAgent());

            return response()->json([
                'valid' => true,
                'source' => 'inventory',
                'item' => [
                    'serial_number' => $item->serial_number,
                    'product_name' => $item->product?->name,
                    'metal' => $item->product?->metal,
                    'karat' => $item->product?->karat,
                    'weight' => $item->actual_weight ?? $item->product?->weight,
                    'purity_tested' => (bool) $item->purity_tested,
                    'status' => $item->status,
                    'sold_at' => $item->sold_at?->toIso8601String(),
                    'scan_count' => $item->scan_count,
                    'first_scanned_at' => $item->first_scanned_at?->toIso8601String(),
                ],
            ]);
        }

        if ($approvedSerial) {
            return response()->json([
                'valid' => true,
                'source' => 'approved_list',
                'item' => [
                    'serial_number' => $approvedSerial->serial_number,
                    'product_name' => $approvedSerial->product_name,
                    'metal' => $approvedSerial->metal,
                    'karat' => $approvedSerial->karat,
                    'weight' => $approvedSerial->weight,
                    'purity' => $approvedSerial->purity,
                ],
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Serial number not found in our records. This piece may not be from Islamabad Bullion Exchange.',
        ]);
    }
}
