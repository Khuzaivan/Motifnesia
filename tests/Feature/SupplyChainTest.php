<?php

namespace Tests\Feature;

use App\Models\ProductSizeStock;
use App\Models\Produk;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockProcurement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplyChainTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $gudang;
    protected User $cashier;
    protected User $customer;
    protected Produk $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user accounts for testing
        $this->owner = User::create([
            'name' => 'test_owner',
            'full_name' => 'Test Owner',
            'email' => 'owner@motifnesia.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'admin_role' => 'owner',
            'account_status' => 'active',
        ]);

        $this->gudang = User::create([
            'name' => 'test_gudang',
            'full_name' => 'Test Gudang',
            'email' => 'gudang@motifnesia.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'admin_role' => 'gudang',
            'account_status' => 'active',
        ]);

        $this->cashier = User::create([
            'name' => 'test_cashier',
            'full_name' => 'Test Cashier',
            'email' => 'cashier@motifnesia.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'admin_role' => 'kasir',
            'account_status' => 'active',
        ]);

        $this->customer = User::create([
            'name' => 'test_customer',
            'full_name' => 'Test Customer',
            'email' => 'customer@motifnesia.test',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'account_status' => 'active',
        ]);

        // Create a test product
        $this->product = Produk::create([
            'nama_produk' => 'Test Batik Shirt',
            'harga' => 150000,
            'stok' => 0,
            'kategori' => 'Pria',
            'is_active' => true,
        ]);

        // Setup size stocks
        foreach (['S', 'M', 'L', 'XL'] as $size) {
            ProductSizeStock::create([
                'produk_id' => $this->product->id,
                'ukuran' => $size,
                'stok' => 10,
            ]);
            WarehouseStock::create([
                'produk_id' => $this->product->id,
                'ukuran' => $size,
                'stok' => 10,
            ]);
        }
        $this->product->update(['stok' => 40]);
    }

    /** @test */
    public function guests_cannot_access_supply_chain_pages()
    {
        $this->get(route('admin.suppliers.index'))->assertRedirect(route('auth.login'));
        $this->get(route('admin.stock-procurements.index'))->assertRedirect(route('auth.login'));
        $this->get(route('admin.warehouse.index'))->assertRedirect(route('auth.login'));
        $this->get(route('admin.stock-opname.index'))->assertRedirect(route('auth.login'));
    }

    /** @test */
    public function only_owner_can_manage_suppliers()
    {
        // Cashier cannot access
        $this->actingAs($this->cashier)
            ->get(route('admin.suppliers.index'))
            ->assertStatus(403);

        // Gudang cannot access
        $this->actingAs($this->gudang)
            ->get(route('admin.suppliers.index'))
            ->assertStatus(403);

        // Owner can access
        $this->actingAs($this->owner)
            ->get(route('admin.suppliers.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function owner_and_gudang_can_manage_stock_and_warehouse()
    {
        // Cashier cannot access
        $this->actingAs($this->cashier)->get(route('admin.stock-procurements.index'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('admin.warehouse.index'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('admin.stock-opname.index'))->assertStatus(403);

        // Gudang can access
        $this->actingAs($this->gudang)->get(route('admin.stock-procurements.index'))->assertStatus(200);
        $this->actingAs($this->gudang)->get(route('admin.warehouse.index'))->assertStatus(200);
        $this->actingAs($this->gudang)->get(route('admin.stock-opname.index'))->assertStatus(200);

        // Owner (who has all permissions) can access
        $this->actingAs($this->owner)->get(route('admin.stock-procurements.index'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('admin.warehouse.index'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('admin.stock-opname.index'))->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_supplier_which_creates_user()
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('admin.suppliers.store'), [
            'name' => 'Batik Supplier Indotama',
            'contact_person' => 'Budi Santoso',
            'email' => 'budi@indotama.test',
            'phone' => '081234567890',
            'address' => 'Sleman, Yogyakarta',
            'status' => 'active',
            'notes' => 'Supplier kain premium',
            'password' => 'supplier123',
        ]);

        $response->assertRedirect(route('admin.suppliers.index'));
        
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Batik Supplier Indotama',
            'email' => 'budi@indotama.test',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'budi@indotama.test',
            'role' => 'supplier',
        ]);
    }

    /** @test */
    public function full_procurement_workflow_succeeds()
    {
        // 1. Create a supplier first
        $supplierUser = User::create([
            'name' => 'indotama_supplier',
            'full_name' => 'Indotama',
            'email' => 'supplier@indotama.test',
            'password' => bcrypt('password123'),
            'role' => 'supplier',
            'account_status' => 'active',
        ]);

        $supplier = Supplier::create([
            'user_id' => $supplierUser->id,
            'name' => 'Batik Indotama',
            'email' => 'supplier@indotama.test',
            'status' => 'active',
        ]);

        // 2. Admin Gudang creates procurement request
        $this->actingAs($this->gudang);

        $response = $this->post(route('admin.stock-procurements.store'), [
            'supplier_id' => $supplier->id,
            'note' => 'Urgent procurement',
            'items' => [
                $this->product->id => [
                    'qty_s' => 5,
                    'qty_m' => 10,
                    'qty_l' => 0,
                    'qty_xl' => 15,
                ]
            ]
        ]);

        $procurement = StockProcurement::latest()->first();
        $response->assertRedirect(route('admin.stock-procurements.show', $procurement));

        $this->assertDatabaseHas('stock_procurements', [
            'id' => $procurement->id,
            'supplier_id' => $supplier->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('stock_procurement_items', [
            'stock_procurement_id' => $procurement->id,
            'produk_id' => $this->product->id,
            'qty_s' => 5,
            'qty_m' => 10,
            'qty_l' => 0,
            'qty_xl' => 15,
            'total_qty' => 30,
        ]);

        // 3. Supplier logs in and approves the procurement
        $this->actingAs($supplierUser);
        
        $response = $this->post(route('supplier.procurements.status', $procurement), [
            'status' => 'approved',
        ]);
        $response->assertRedirect();
        $this->assertEquals('approved', $procurement->fresh()->status);

        // 4. Supplier marks as in delivery
        $response = $this->post(route('supplier.procurements.status', $procurement), [
            'status' => 'in_delivery',
        ]);
        $response->assertRedirect();
        $this->assertEquals('in_delivery', $procurement->fresh()->status);

        // 5. Admin Gudang confirms arrival
        $this->actingAs($this->gudang);

        $response = $this->post(route('admin.stock-procurements.confirm-arrived', $procurement));
        $response->assertRedirect();
        $this->assertEquals('arrived', $procurement->fresh()->status);

        // Record stock counts before applying
        $sBeforeSys = ProductSizeStock::where('produk_id', $this->product->id)->where('ukuran', 'S')->first()->stok;
        $sBeforeWh = WarehouseStock::where('produk_id', $this->product->id)->where('ukuran', 'S')->first()->stok;
        
        $xlBeforeSys = ProductSizeStock::where('produk_id', $this->product->id)->where('ukuran', 'XL')->first()->stok;
        $xlBeforeWh = WarehouseStock::where('produk_id', $this->product->id)->where('ukuran', 'XL')->first()->stok;

        // 6. Admin Gudang applies stock to system & warehouse
        $response = $this->post(route('admin.stock-procurements.apply-stock', $procurement));
        $response->assertRedirect(route('admin.stock-procurements.show', $procurement));
        $this->assertEquals('stock_applied', $procurement->fresh()->status);

        // Verify stock has increased in both product_size_stocks and warehouse_stocks
        $this->assertEquals($sBeforeSys + 5, ProductSizeStock::where('produk_id', $this->product->id)->where('ukuran', 'S')->first()->stok);
        $this->assertEquals($sBeforeWh + 5, WarehouseStock::where('produk_id', $this->product->id)->where('ukuran', 'S')->first()->stok);

        $this->assertEquals($xlBeforeSys + 15, ProductSizeStock::where('produk_id', $this->product->id)->where('ukuran', 'XL')->first()->stok);
        $this->assertEquals($xlBeforeWh + 15, WarehouseStock::where('produk_id', $this->product->id)->where('ukuran', 'XL')->first()->stok);

        // Total global product stock should increase by 30
        $this->assertEquals(70, $this->product->fresh()->stok);

        // Check stock movement records
        $this->assertDatabaseHas('stock_movements', [
            'produk_id' => $this->product->id,
            'ukuran' => 'S',
            'movement_type' => 'procurement_applied',
            'qty_change' => 5,
        ]);
    }

    /** @test */
    public function stock_opname_aligns_system_stock_to_warehouse_stock()
    {
        $this->actingAs($this->gudang);

        // Set discrepancy manually in database
        // Warehouse S stock is 10, but System S stock is 15
        ProductSizeStock::where('produk_id', $this->product->id)->where('ukuran', 'S')->update(['stok' => 15]);
        $this->product->update(['stok' => 45]);

        $response = $this->post(route('admin.stock-opname.adjust'), [
            'produk_id' => $this->product->id,
            'ukuran' => 'S',
            'note' => 'Opname penyesuaian selisih S',
        ]);

        $response->assertRedirect(route('admin.stock-opname.index'));

        // Verify system stock has been set to warehouse stock (which was 10)
        $this->assertEquals(10, ProductSizeStock::where('produk_id', $this->product->id)->where('ukuran', 'S')->first()->stok);

        // Global stock should update
        $this->assertEquals(40, $this->product->fresh()->stok);

        // Verify opname and movement logs are recorded
        $this->assertDatabaseHas('stock_opnames', [
            'produk_id' => $this->product->id,
            'ukuran' => 'S',
            'system_stock_before' => 15,
            'warehouse_stock_before' => 10,
            'system_stock_after' => 10,
            'difference' => -5,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'produk_id' => $this->product->id,
            'ukuran' => 'S',
            'movement_type' => 'stock_opname',
            'qty_change' => -5,
        ]);
    }
}
