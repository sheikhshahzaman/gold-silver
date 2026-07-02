<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryDeletionTest extends TestCase
{
    use RefreshDatabase;

    private function makeItem(string $serial): InventoryItem
    {
        $category = ProductCategory::create(['name' => 'Gold Bars', 'slug' => 'gold-bars-' . uniqid()]);
        $product = Product::create([
            'name' => '24K Gold Bar',
            'slug' => '24k-gold-bar-' . uniqid(),
            'weight' => '1 Tola',
            'metal' => 'gold',
            'karat' => '24K',
            'category_id' => $category->id,
            'price_type' => 'fixed',
            'fixed_price' => 445600,
            'is_active' => true,
        ]);

        return InventoryItem::create([
            'product_id' => $product->id,
            'verification_token' => bin2hex(random_bytes(16)),
            'serial_number' => $serial,
            'status' => 'in_stock',
        ]);
    }

    public function test_admin_can_delete_inventory_item_via_row_action(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = $this->makeItem('IBE-TEST-000001');

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\InventoryItemResource\Pages\ListInventoryItems::class)
            ->callTableAction('delete', $item)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('inventory_items', ['id' => $item->id]);
    }

    public function test_admin_can_bulk_delete_inventory_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item1 = $this->makeItem('IBE-TEST-000002');
        $item2 = $this->makeItem('IBE-TEST-000003');

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\InventoryItemResource\Pages\ListInventoryItems::class)
            ->callTableBulkAction('delete', [$item1, $item2])
            ->assertHasNoTableBulkActionErrors();

        $this->assertDatabaseMissing('inventory_items', ['id' => $item1->id]);
        $this->assertDatabaseMissing('inventory_items', ['id' => $item2->id]);
    }
}
