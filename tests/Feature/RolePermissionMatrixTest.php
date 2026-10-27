<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionMatrixTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private User $customer;
    private Category $category;
    private Supplier $supplier;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator Utama',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Toko',
            'username' => 'staff_test',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $this->customer = User::create([
            'name' => 'Customer Biasa',
            'username' => 'customer_test',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $this->category = Category::create([
            'name' => 'Elektronik',
            'description' => 'Gadget dan komputer',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'PT Supplier Utama',
            'contact_name' => 'Budi',
            'phone' => '08123456789',
            'address' => 'Jakarta Barat',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'TEST-001',
            'name' => 'Barang Uji Coba',
            'purchase_price' => 50000,
            'selling_price' => 75000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);
    }

    /** 1. Verifikasi Akses Role Admin: Memiliki akses ke seluruh modul dan CRUD */
    public function test_admin_has_full_access_to_all_modules(): void
    {
        $this->actingAs($this->admin);

        // Dashboard
        $this->get('/dashboard')->assertStatus(200);

        // Master Data CRUD Form
        $this->get('/products/create')->assertStatus(200);
        $this->get("/products/{$this->product->id}/edit")->assertStatus(200);
        $this->get('/categories/create')->assertStatus(200);
        $this->get("/categories/{$this->category->id}/edit")->assertStatus(200);
        $this->get('/suppliers/create')->assertStatus(200);
        $this->get("/suppliers/{$this->supplier->id}/edit")->assertStatus(200);
        $this->get('/users')->assertStatus(200);
        $this->get('/users/create')->assertStatus(200);

        // Transaksi
        $this->get('/purchases')->assertStatus(200);
        $this->get('/purchases/create')->assertStatus(200);
        $this->get('/sales')->assertStatus(200);
        $this->get('/sales/create')->assertStatus(200);
        $this->get('/stock-movements')->assertStatus(200);
        $this->get('/stock-reports')->assertStatus(200);

        // Public Catalog
        $this->get('/catalog')->assertStatus(200);
    }

    /** 2. Verifikasi Akses Role Staff: Hanya operasional, tidak bisa kelola user / delete master / cancel */
    public function test_staff_has_restricted_access(): void
    {
        $this->actingAs($this->staff);

        // Staff boleh melihat dashboard & katalog
        $this->get('/dashboard')->assertStatus(200);
        $this->get('/catalog')->assertStatus(200);

        // Staff boleh melihat daftar produk, kategori, supplier
        $this->get('/products')->assertStatus(200);
        $this->get("/products/{$this->product->id}")->assertStatus(200);
        $this->get('/categories')->assertStatus(200);
        $this->get("/categories/{$this->category->id}")->assertStatus(200);
        $this->get('/suppliers')->assertStatus(200);
        $this->get("/suppliers/{$this->supplier->id}")->assertStatus(200);

        // Staff boleh transaksi kasir dan pembelian
        $this->get('/sales/create')->assertStatus(200);
        $this->get('/purchases/create')->assertStatus(200);
        $this->get('/stock-movements')->assertStatus(200);
        $this->get('/stock-reports')->assertStatus(200);

        // Staff DILARANG mengakses manajemen user (403)
        $this->get('/users')->assertStatus(403);
        $this->get('/users/create')->assertStatus(403);

        // Staff DILARANG tambah/edit produk (403)
        $this->get('/products/create')->assertStatus(403);
        $this->get("/products/{$this->product->id}/edit")->assertStatus(403);

        // Staff DILARANG tambah/edit kategori (403)
        $this->get('/categories/create')->assertStatus(403);
        $this->get("/categories/{$this->category->id}/edit")->assertStatus(403);

        // Staff DILARANG tambah/edit supplier (403)
        $this->get('/suppliers/create')->assertStatus(403);
        $this->get("/suppliers/{$this->supplier->id}/edit")->assertStatus(403);
    }

    /** 3. Verifikasi Akses Role Customer: Hanya melihat katalog offline dan cek struk, dashboard dialihkan */
    public function test_customer_role_is_directed_to_catalog(): void
    {
        $this->actingAs($this->customer);

        // Akses dashboard dialihkan ke katalog (/shop)
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('customer.shop'));

        // Customer dapat melihat katalog produk dan rincian produk
        $this->get('/catalog')->assertStatus(200)->assertSee('Barang Uji Coba');
        $this->get("/catalog/{$this->product->id}")->assertStatus(200)->assertSee('TEST-001');

        // Route receipt-lookup dialihkan ke katalog
        $this->get('/receipt-lookup')->assertRedirect(route('catalog.index'));

        // Customer DILARANG mengakses modul admin/staff
        $this->get('/users')->assertStatus(403);
        $this->get('/products/create')->assertStatus(403);
        $this->get('/categories/create')->assertStatus(403);
        $this->get('/suppliers/create')->assertStatus(403);
    }

    /** 4. Verifikasi Guest (Pengunjung Belum Login): Bisa melihat katalog, dashboard dialihkan ke login */
    public function test_guest_can_access_catalog(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Katalog Produk');
        $this->get('/catalog')->assertStatus(200);
        $this->get("/catalog/{$this->product->id}")->assertStatus(200);
        $this->get('/receipt-lookup')->assertRedirect(route('catalog.index'));

        // Guest mencoba akses halaman terproteksi dialihkan ke login
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/sales')->assertRedirect(route('login'));
        $this->get('/purchases')->assertRedirect(route('login'));
    }
}
