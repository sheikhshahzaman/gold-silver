<?php

namespace Tests\Feature;

use App\Models\PriceMargin;
use App\Models\Product;
use App\Models\StaffAuditLog;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class StaffAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_only_use_assigned_admin_actions(): void
    {
        $staff = User::factory()->make([
            'role' => 'staff',
            'is_active' => true,
            'staff_permissions' => ['products.view'],
        ]);

        $admin = User::factory()->make([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->assertTrue($staff->canAccessPanel(new Panel()));
        $this->assertTrue(Gate::forUser($staff)->allows('viewAny', Product::class));
        $this->assertFalse(Gate::forUser($staff)->allows('update', new Product()));
        $this->assertFalse(Gate::forUser($staff)->allows('viewAny', PriceMargin::class));
        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', PriceMargin::class));
    }

    public function test_admin_panel_changes_are_audited(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $priceMargin = PriceMargin::create([
            'metal' => 'gold',
            'karat' => '24k',
            'buy_margin' => 100,
            'sell_margin' => 90,
        ]);

        $this->actingAs($admin);
        request()->headers->set('referer', url('/admin/price-margins'));

        $priceMargin->update(['buy_margin' => 125]);

        $this->assertDatabaseHas('staff_audit_logs', [
            'user_id' => $admin->id,
            'feature' => 'price_margins',
            'action' => 'edit',
            'auditable_type' => $priceMargin->getMorphClass(),
            'auditable_id' => (string) $priceMargin->id,
        ]);

        $this->assertSame(1, StaffAuditLog::count());
    }
}
