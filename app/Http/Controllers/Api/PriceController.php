<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Rates\RatesProvider;
use Illuminate\Http\JsonResponse;

class PriceController extends Controller
{
    public function index(RatesProvider $rates): JsonResponse
    {
        return response()->json($rates->getAllPrices(), 200, ['Cache-Control' => 'no-store']);
    }
}
