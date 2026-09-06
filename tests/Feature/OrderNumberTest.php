<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Support\OrderNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OrderNumberTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(array $attributes = []): Order
    {
        $order = new Order();
        $order->forceFill(array_merge([
            'customer_name' => 'Test Customer',
            'customer_phone' => '03001234567',
            'type' => 'buy',
            'status' => Order::STATUS_PENDING,
            'total_amount' => 1000,
        ], $attributes));
        $order->save();

        return $order->refresh();
    }

    public function test_generated_numbers_match_the_ibe_format(): void
    {
        $number = OrderNumber::generate(Carbon::parse('2026-09-06'));

        $this->assertMatchesRegularExpression('/^IBE-\d{5}-\d{8}$/', $number);

        // 2026 => "26"; 6 September is day 249 of the year => "249".
        $this->assertStringStartsWith('IBE-26249-', $number);
    }

    public function test_generated_numbers_are_unique(): void
    {
        $seen = [];

        for ($i = 0; $i < 30; $i++) {
            $number = OrderNumber::generate();
            $this->assertNotContains($number, $seen, 'Duplicate order number generated.');
            $seen[] = $number;

            $this->makeOrder(['order_number' => $number]);
        }
    }

    public function test_normalize_accepts_however_the_customer_types_it(): void
    {
        $inputs = [
            'canonical' => 'IBE-26249-40571836',
            'lower case' => 'ibe-26249-40571836',
            'mixed case' => 'Ibe-26249-40571836',
            'no prefix' => '26249-40571836',
            'no hyphens' => 'IBE2624940571836',
            'digits only' => '2624940571836',
            'spaces' => 'ibe 26249 40571836',
            'padded' => '  IBE-26249-40571836  ',
            'extra digits trimmed' => 'IBE-26249-4057183699',
        ];

        foreach ($inputs as $label => $input) {
            $this->assertSame(
                'IBE-26249-40571836',
                OrderNumber::normalize($input),
                "Failed for input style: {$label}"
            );
        }
    }

    public function test_legacy_order_numbers_still_normalize(): void
    {
        $this->assertSame(
            'ORD-AB12CD34-1712345678',
            OrderNumber::normalize('ord-ab12cd34-1712345678')
        );
    }

    public function test_empty_input_is_empty(): void
    {
        $this->assertSame('', OrderNumber::normalize(''));
        $this->assertSame('', OrderNumber::normalize('---'));
    }

    public function test_order_model_assigns_the_new_format(): void
    {
        $order = $this->makeOrder();

        $this->assertMatchesRegularExpression('/^IBE-\d{5}-\d{8}$/', $order->order_number);
    }

    public function test_a_typed_lower_case_number_finds_the_order(): void
    {
        $order = $this->makeOrder();

        $found = Order::where(
            'order_number',
            OrderNumber::normalize(strtolower(str_replace('-', '', $order->order_number)))
        )->first();

        $this->assertNotNull($found, 'Lower case, hyphen-free input did not find the order.');
        $this->assertSame($order->id, $found->id);
    }
}
