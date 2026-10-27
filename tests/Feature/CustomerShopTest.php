<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerShopTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;
    private Category $category;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Elektronik Kantor',
            'description' => 'Aksesoris dan periferal kantor',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'MOUSE-W-01',
            'name' => 'Logitech Wireless Mouse M185',
            'description' => 'Mouse wireless 2.4GHz dengan sensor optik presisi.',
            'purchase_price' => 120000,
            'selling_price' => 185000,
            'stock' => 15,
            'minimum_stock' => 3,
        ]);

        $this->staff = User::create([
            'name' => 'Staff Kasir',
            'username' => 'staff_kasir',
            'email' => 'staff@example.com',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);
    }

    /** 1. Customer dapat melihat etalase katalog dan halaman rincian produk */
    public function test_customer_can_view_catalog_storefront_and_product_detail(): void
    {
        $response = $this->get('/catalog');
        $response->assertStatus(200);
        $response->assertSee('Logitech Wireless Mouse M185');
        $response->assertSee('185.000');

        $detailResponse = $this->get("/catalog/{$this->product->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Logitech Wireless Mouse M185');
        $detailResponse->assertSee('Nomor SKU');
    }

    /** 2. Detail produk memuat spesifikasi dan ketersediaan stok */
    public function test_customer_sees_offline_purchase_guide(): void
    {
        $detailResponse = $this->get("/catalog/{$this->product->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('MOUSE-W-01');
        $detailResponse->assertSee('unit siap di toko');
    }

    /** 3. Customer dapat mencari produk berdasarkan nama atau filter kategori */
    public function test_customer_can_search_and_filter_products(): void
    {
        $resFound = $this->get('/catalog?search=Logitech');
        $resFound->assertStatus(200);
        $resFound->assertSee('Logitech Wireless Mouse M185');

        $resNotFound = $this->get('/catalog?search=BarangTidakAda12345');
        $resNotFound->assertStatus(200);
        $resNotFound->assertSee('Tidak ada produk yang cocok');
    }

    /** 4. Route pencarian struk dialihkan langsung ke katalog produk */
    public function test_receipt_lookup_redirects_to_catalog(): void
    {
        $response = $this->get('/receipt-lookup');
        $response->assertRedirect(route('catalog.index'));

        $trackResponse = $this->get('/orders/track');
        $trackResponse->assertRedirect(route('catalog.index'));
    }

    /** 5. Tampilan struk pembayaran kasir offline resmi dapat diakses dan dicetak */
    public function test_customer_can_view_printable_official_receipt(): void
    {
        $sale = Sale::create([
            'customer_name' => 'Siti Aminah',
            'customer_phone' => '08987654321',
            'customer_address' => 'Ambil di Toko',
            'sale_number' => 'SALE-20261025-0002',
            'total' => 185000,
            'payment_method' => 'qris',
            'payment_status' => 'paid',
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $sale->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 185000,
            'subtotal' => 185000,
        ]);

        $receiptRes = $this->get("/receipt/{$sale->sale_number}");
        $receiptRes->assertStatus(200);
        $receiptRes->assertSee('INVENTORY STORE INDONESIA');
        $receiptRes->assertSee('SALE-20261025-0002');
        $receiptRes->assertSee('Siti Aminah');
        $receiptRes->assertSee('QRIS');
        $receiptRes->assertSee('185.000');
    }

    /** 6. Kasir offline oleh staf/admin memproses transaksi langsung di toko dan mengurangi stok */
    public function test_cashier_offline_sales_flow_reduces_stock_and_creates_receipt(): void
    {
        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_name' => 'Ahmad Pelanggan Toko',
            'customer_phone' => '081299887766',
            'payment_method' => 'cash',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response->assertRedirect(route('sales.index'));

        // Stok awal 15 berkurang 3 menjadi 12
        $this->assertEquals(12, $this->product->fresh()->stock);

        $sale = Sale::where('customer_phone', '081299887766')->first();
        $this->assertNotNull($sale);
        $this->assertEquals(555000, (float) $sale->total); // 3 * 185000
        $this->assertEquals('completed', $sale->status);
    }
}
