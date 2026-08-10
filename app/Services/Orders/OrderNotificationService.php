<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderNotificationService
{
    public function notifyOrderSubmitted(Order $order): void
    {
        $order->loadMissing('items', 'payment');

        $message = $this->messageFor($order);

        Log::info('Order notification', [
            'order_number' => $order->order_number,
            'customer_phone' => $order->customer_phone,
            'message' => $message,
        ]);

        $enabled = Setting::get('order_sms_enabled', '0') === '1';
        $webhookUrl = trim((string) Setting::get('order_sms_webhook_url', config('services.order_sms.webhook_url', '')));
        if (! $enabled || $webhookUrl === '') {
            return;
        }

        try {
            $request = Http::timeout(10);
            $token = trim((string) Setting::get('order_sms_auth_token', ''));
            if ($token !== '') {
                $request = $request->withToken($token);
            }

            $response = $request->post($webhookUrl, [
                'order_number' => $order->order_number,
                'to' => Setting::get('order_sms_to', config('services.order_sms.to')) ?: $order->customer_phone,
                'customer_phone' => $order->customer_phone,
                'customer_name' => $order->customer_name,
                'message' => $message,
                'total' => (float) $order->grand_total,
                'status' => $order->display_status,
            ]);

            if ($response->failed()) {
                Log::warning('Order notification webhook returned an error', [
                    'order_number' => $order->order_number,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Order notification webhook failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function messageFor(Order $order): string
    {
        $template = (string) Setting::get(
            'order_sms_template',
            'New order {order_number}: {customer_name} ({customer_phone}), total Rs {total}.'
        );

        $values = [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name ?: 'Customer',
            'customer_phone' => $order->customer_phone ?: '-',
            'total' => number_format((float) $order->grand_total, 0),
            'status' => $order->display_status,
        ];

        foreach ($values as $key => $value) {
            $template = str_replace('{' . $key . '}', (string) $value, $template);
        }

        return $template;
    }
}
