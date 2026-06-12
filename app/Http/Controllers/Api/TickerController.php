<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsTicker;
use Illuminate\Http\JsonResponse;

class TickerController extends Controller
{
    /**
     * Active marquee headlines for the app's ticker.
     */
    public function index(): JsonResponse
    {
        $items = NewsTicker::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (NewsTicker $t) => [
                'id' => $t->id,
                'title' => $t->title,
                'content' => $t->content,
            ]);

        return response()->json([
            'headlines' => $items,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
