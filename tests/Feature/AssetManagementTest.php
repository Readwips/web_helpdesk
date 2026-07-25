<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $technician;

    private User $user;

    private AssetCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->technician = User::factory()->create(['role' => 'technician']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->category = AssetCategory::create(['name' => 'Laptop', 'code' => 'LPT']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge(['asset_category_id' => $this->category->id, 'name' => 'Laptop Operasional', 'brand' => 'Lenovo', 'model' => 'ThinkPad T14', 'serial_number' => 'SERIAL-001', 'location' => 'Ruang IT', 'condition' => 'baik', 'status' => 'tersedia'], $overrides);
    }

    private function asset(array $overrides = []): Asset
    {
        return Asset::create(array_merge($this->payload(), ['asset_code' => 'AST-LPT-0001'], $overrides));
    }

    public function test_admin_creates_assets_with_automatic_unique_codes(): void
    {
        $this->actingAs($this->admin)->post(route('assets.store'), $this->payload())->assertRedirect();
        $this->actingAs($this->admin)->post(route('assets.store'), $this->payload(['serial_number' => 'SERIAL-002']))->assertRedirect();
        $this->assertDatabaseHas('assets', ['asset_code' => 'AST-LPT-0001']);
        $this->assertDatabaseHas('assets', ['asset_code' => 'AST-LPT-0002']);
    }

    public function test_duplicate_serial_number_is_rejected(): void
    {
        $this->asset();
        $this->actingAs($this->admin)->post(route('assets.store'), $this->payload())->assertSessionHasErrors('serial_number');
        $this->assertSame(1, Asset::count());
    }

    public function test_asset_assignment_is_transactional_and_cannot_be_duplicated(): void
    {
        $asset = $this->asset();
        $data = ['user_id' => $this->user->id, 'status' => 'digunakan', 'notes' => 'Laptop kerja'];
        $this->actingAs($this->admin)->post(route('assets.assign.store', $asset), $data)->assertRedirect();
        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'assigned_user_id' => $this->user->id, 'status' => 'digunakan']);
        $this->actingAs($this->admin)->post(route('assets.assign.store', $asset), $data)->assertSessionHasErrors('asset');
        $this->assertSame(1, $asset->assignments()->whereNull('returned_at')->count());
    }

    public function test_repaired_or_heavily_damaged_asset_cannot_be_assigned(): void
    {
        $asset = $this->asset(['status' => 'diperbaiki']);
        $this->actingAs($this->admin)->post(route('assets.assign.store', $asset), ['user_id' => $this->user->id, 'status' => 'digunakan'])->assertSessionHasErrors('asset');
        $asset->update(['status' => 'tersedia', 'condition' => 'rusak_berat']);
        $this->actingAs($this->admin)->post(route('assets.assign.store', $asset), ['user_id' => $this->user->id, 'status' => 'digunakan'])->assertSessionHasErrors('asset');
    }

    public function test_return_closes_active_assignment_and_preserves_history(): void
    {
        $asset = $this->asset();
        $this->actingAs($this->admin)->post(route('assets.assign.store', $asset), ['user_id' => $this->user->id, 'status' => 'dipinjamkan']);
        $this->actingAs($this->admin)->put(route('assets.return', $asset), ['condition' => 'perlu_perawatan', 'notes' => 'Kipas bising'])->assertRedirect();
        $this->assertNull($asset->refresh()->assigned_user_id);
        $this->assertNotNull($asset->assignments()->first()->returned_at);
        $this->assertSame('perlu_perawatan', $asset->condition);
    }

    public function test_user_only_sees_their_assigned_asset(): void
    {
        $mine = $this->asset(['assigned_user_id' => $this->user->id, 'status' => 'digunakan']);
        $other = Asset::create($this->payload(['asset_code' => 'AST-LPT-0002', 'serial_number' => 'SERIAL-002', 'name' => 'ASET ORANG LAIN']));
        $this->actingAs($this->user)->get(route('assets.index'))->assertOk()->assertSee($mine->asset_code)->assertDontSee($other->name);
        $this->actingAs($this->user)->get(route('assets.show', $other))->assertForbidden();
    }

    public function test_technician_records_repair_cost_and_updates_asset(): void
    {
        $asset = $this->asset(['status' => 'diperbaiki']);
        $response = $this->actingAs($this->technician)->post(route('repairs.store', $asset), ['repair_date' => now()->format('Y-m-d'), 'complaint' => 'Tidak menyala', 'diagnosis' => 'Adaptor rusak', 'repair_action' => 'Mengganti adaptor', 'repair_cost' => 350000, 'result' => 'berhasil', 'asset_condition' => 'baik', 'asset_status' => 'tersedia']);
        $response->assertRedirect(route('assets.show', $asset));
        $this->assertDatabaseHas('asset_repairs', ['asset_id' => $asset->id, 'technician_id' => $this->technician->id, 'repair_cost' => 350000]);
        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'condition' => 'baik', 'status' => 'tersedia']);
    }
}
