<?php

namespace App\Services\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderNotificationService
{
    public function notifyOrderSubmitted(Order $order): void
    {
        $order->loadMissing('items', 'payment');

        $message = "New order {$order->order_number}: {$order->customer_name} ({$order->customer_phone}), "
            . "{$order->display_status}, total Rs " . number_format((float) $order->grand_total, 0) . '.';

        Log::info('Order notification', [
            'order_number' => $order->order_number,
            'customer_phone' => $order->customer_phone,
            'message' => $message,
        ]);

        $webhookUrl = config('services.order_sms.webhook_url');
        if (! $webhookUrl) {
            return;
        }

        try {
            Http::timeout(10)->post($webhookUrl, [
                'order_number' => $order->order_number,
                'to' => config('services.order_sms.to') ?: $order->customer_phone,
                'customer_phone' => $order->customer_phone,
                'customer_name' => $order->customer_name,
                'message' => $message,
                'total' => (float) $order->grand_total,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Order notification webhook failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
