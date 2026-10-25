<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private Category $category;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Test',
            'username' => 'staff_test',
            'email' => 'staff@test.com',
            'password' => Hash::make('secret123'),
            'role' => 'staff',
        ]);

        $this->category = Category::create([
            'name' => 'Elektronik',
            'description' => 'Barang elektronik',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'PT Supplier Utama',
            'contact_person' => 'Budi',
            'phone' => '08123456789',
            'email' => 'supplier@test.com',
        ]);
    }

    /** 1. Auth: Guest tidak dapat membuka dashboard; password salah ditolak */
    public function test_guest_cannot_access_dashboard_and_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin_test',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin_test',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    /** 2. Product: SKU duplikat ditolak; harga negatif ditolak */
    public function test_product_sku_must_be_unique_and_prices_must_be_positive(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'sku' => 'SKU-001',
            'name' => 'Produk A',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);

        // Coba buat SKU sama
        $response = $this->actingAs($this->admin)->post('/products', [
            'category_id' => $this->category->id,
            'sku' => 'SKU-001',
            'name' => 'Produk Duplikat',
            'purchase_price' => 5000,
            'selling_price' => 8000,
            'stock' => 5,
            'minimum_stock' => 1,
        ]);
        $response->assertSessionHasErrors('sku');

        // Coba harga negatif
        $responseNeg = $this->actingAs($this->admin)->post('/products', [
            'category_id' => $this->category->id,
            'sku' => 'SKU-002',
            'name' => 'Produk Negatif',
            'purchase_price' => -1000,
            'selling_price' => -500,
            'stock' => 5,
            'minimum_stock' => 1,
        ]);
        $responseNeg->assertSessionHasErrors(['purchase_price', 'selling_price']);
    }

    /** 3. Purchase: Pembelian sukses menambah stok sesuai kuantitas */
    public function test_purchase_transaction_increases_stock_atomically(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'PUR-TEST-1',
            'name' => 'Monitor LED',
            'purchase_price' => 1000000,
            'selling_price' => 1300000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);

        $response = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'unit_cost' => 950000,
                ],
            ],
        ]);

        $response->assertRedirect(route('purchases.index'));
        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $this->supplier->id,
            'total' => 9500000,
        ]);

        $product->refresh();
        $this->assertEquals(15, $product->stock); // 5 + 10
        $this->assertEquals(950000, (float) $product->purchase_price);
    }

    /** 4. Sale: Penjualan sukses mengurangi stok sesuai kuantitas */
    public function test_sale_transaction_decreases_stock_atomically(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'SALE-TEST-1',
            'name' => 'Headset Gaming',
            'purchase_price' => 150000,
            'selling_price' => 200000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);

        $response = $this->actingAs($this->staff)->post('/sales', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 7,
                ],
            ],
        ]);

        $response->assertRedirect(route('sales.index'));
        $this->assertDatabaseHas('sales', [
            'total' => 1400000, // 7 * 200000 (harga dihitung di server!)
        ]);

        $product->refresh();
        $this->assertEquals(13, $product->stock); // 20 - 7
    }

    /** 5. Insufficient stock: Penjualan dengan stok tidak cukup ditolak tanpa perubahan data */
    public function test_sale_fails_when_stock_is_insufficient_without_data_corruption(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'STOCK-LIMIT-1',
            'name' => 'Flashdisk 64GB',
            'purchase_price' => 50000,
            'selling_price' => 75000,
            'stock' => 3,
            'minimum_stock' => 1,
        ]);

        $response = $this->actingAs($this->staff)->post('/sales', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5, // melebihi stok 3
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);

        $product->refresh();
        $this->assertEquals(3, $product->stock); // Stok tidak boleh berubah
    }

    /** 6. Atomicity: Jika satu item transaksi gagal, seluruh transaksi rollback */
    public function test_transaction_atomicity_rolls_back_if_any_item_fails(): void
    {
        $productA = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'PROD-A',
            'name' => 'Produk Cukup',
            'purchase_price' => 10000,
            'selling_price' => 20000,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);

        $productB = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'PROD-B',
            'name' => 'Produk Kurang',
            'purchase_price' => 10000,
            'selling_price' => 20000,
            'stock' => 2,
            'minimum_stock' => 2,
        ]);

        // Kirim transaksi berisi 2 item: item 1 valid, item 2 melebihi stok
        $response = $this->actingAs($this->staff)->post('/sales', [
            'items' => [
                [
                    'product_id' => $productA->id,
                    'quantity' => 4,
                ],
                [
                    'product_id' => $productB->id,
                    'quantity' => 10, // gagal disini
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        // Pastikan tidak ada data yang tersimpan sama sekali (Rollback atomik)
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);

        $productA->refresh();
        $productB->refresh();
        $this->assertEquals(10, $productA->stock); // Product A TIDAK boleh terpotong
        $this->assertEquals(2, $productB->stock);
    }

    /** 7. Authorization: Staff tidak dapat mengakses fungsi Admin */
    public function test_staff_cannot_access_admin_functions(): void
    {
        // Staff coba buka form tambah produk
        $resProductCreate = $this->actingAs($this->staff)->get('/products/create');
        $resProductCreate->assertForbidden();

        // Staff coba buka form tambah kategori
        $resCatCreate = $this->actingAs($this->staff)->get('/categories/create');
        $resCatCreate->assertForbidden();

        // Staff coba akses User Management
        $resUsers = $this->actingAs($this->staff)->get('/users');
        $resUsers->assertForbidden();
    }

    /** 8. Search & Filter: Filter dan pagination menghasilkan data yang sesuai */
    public function test_search_and_filter_yields_correct_data(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'sku' => 'SEARCH-1',
            'name' => 'Kamera Web HD',
            'purchase_price' => 200000,
            'selling_price' => 300000,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);

        Product::create([
            'category_id' => $this->category->id,
            'sku' => 'SEARCH-2',
            'name' => 'Microphone Condenser',
            'purchase_price' => 150000,
            'selling_price' => 250000,
            'stock' => 5,
            'minimum_stock' => 1,
        ]);

        $response = $this->actingAs($this->staff)->get('/products?search=Kamera');
        $response->assertStatus(200);
        $response->assertSee('Kamera Web HD');
        $response->assertDontSee('Microphone Condenser');
    }

    /** 9. Transaksi Pembatalan Sales Mengembalikan Stok */
    public function test_admin_can_cancel_sale_and_restore_stock(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'CANCEL-SALE',
            'name' => 'Barang Batal Jual',
            'purchase_price' => 50000,
            'selling_price' => 80000,
            'stock' => 15,
            'minimum_stock' => 2,
        ]);

        // Buat sale 5 unit -> sisa stok 10
        $this->actingAs($this->staff)->post('/sales', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ],
            ],
        ]);

        $sale = Sale::first();
        $this->assertEquals(10, $product->fresh()->stock);

        // Batalkan sale oleh Admin
        $response = $this->actingAs($this->admin)->post("/sales/{$sale->id}/cancel");
        $response->assertRedirect(route('sales.index'));

        $this->assertEquals('cancelled', $sale->fresh()->status);
        $this->assertEquals(15, $product->fresh()->stock); // Stok kembali 15
    }

    /** 10. Larangan Hapus Data yang Memiliki Transaksi */
    public function test_cannot_delete_product_used_in_transaction(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'CANNOT-DEL',
            'name' => 'Produk Berelasi',
            'purchase_price' => 10000,
            'selling_price' => 20000,
            'stock' => 5,
            'minimum_stock' => 1,
        ]);

        // Lakukan sale
        $this->actingAs($this->staff)->post('/sales', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        // Coba hapus oleh Admin
        $response = $this->actingAs($this->admin)->delete("/products/{$product->id}");
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('error');

        // Pastikan produk tetap ada di database
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /** 11. Larangan Hapus Supplier yang Memiliki Pembelian */
    public function test_cannot_delete_supplier_with_purchases(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'SUP-PUR-1',
            'name' => 'Produk Supplier',
            'purchase_price' => 10000,
            'selling_price' => 20000,
            'stock' => 5,
            'minimum_stock' => 1,
        ]);

        $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_cost' => 10000,
                ],
            ],
        ]);

        $response = $this->actingAs($this->admin)->delete("/suppliers/{$this->supplier->id}");
        $response->assertRedirect(route('suppliers.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('suppliers', ['id' => $this->supplier->id]);
    }

    /** 12. Admin dapat membuat dan mengelola user baru */
    public function test_admin_can_create_and_manage_users(): void
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'User Baru',
            'username' => 'user_baru',
            'email' => 'baru@test.com',
            'password' => 'secret123',
            'role' => 'staff',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'user_baru',
            'role' => 'staff',
        ]);
    }

    /** 13. Staff dapat melihat daftar kategori dan supplier (read-only) */
    public function test_staff_can_view_categories_and_suppliers_list(): void
    {
        $resCat = $this->actingAs($this->staff)->get('/categories');
        $resCat->assertStatus(200);

        $resSup = $this->actingAs($this->staff)->get('/suppliers');
        $resSup->assertStatus(200);
    }

    /** 14. Customer diarahkan ke etalase toko dan tidak bisa akses dashboard internal */
    public function test_customer_role_is_redirected_to_shop_when_accessing_dashboard(): void
    {
        $customerUser = User::create([
            'name' => 'Pelanggan Test',
            'username' => 'pelanggan_test',
            'email' => 'pelanggan@test.com',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customerUser)->get('/dashboard');
        $response->assertRedirect(route('customer.shop'));
    }

    /** 15. Admin dan Staff dapat melihat & mencari transaksi customer di daftar sales */
    public function test_admin_and_staff_can_see_and_search_customer_orders(): void
    {
        Sale::create([
            'customer_name' => 'Dewi Anggraini',
            'customer_phone' => '087788990011',
            'customer_address' => 'Bandung',
            'sale_number' => 'SALE-20261025-0077',
            'total' => 350000,
            'payment_method' => 'transfer_bca',
            'payment_status' => 'paid',
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        // Cek Admin dapat melihat dan mencari nama customer
        $resAdmin = $this->actingAs($this->admin)->get('/sales?search=Dewi');
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Dewi Anggraini');
        $resAdmin->assertSee('SALE-20261025-0077');

        // Cek Staff juga dapat melihat transaksi penjualan
        $resStaff = $this->actingAs($this->staff)->get('/sales');
        $resStaff->assertStatus(200);
        $resStaff->assertSee('Dewi Anggraini');
    }
}
