<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('name', 'Elektronik')->first();
        $stationery = Category::where('name', 'Alat Tulis')->first();
        $office = Category::where('name', 'Perlengkapan Kantor')->first();

        $products = [
            [
                'category_id' => $electronics?->id ?? 1,
                'sku' => 'ELK-001',
                'name' => 'Mouse Wireless Logitech M185',
                'description' => 'Mouse tanpa kabel ergonomis 2.4 GHz dengan koneksi wireless stabil hingga jarak 10 meter.',
                'purchase_price' => 110000,
                'selling_price' => 150000,
                'stock' => 25,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $electronics?->id ?? 1,
                'sku' => 'ELK-002',
                'name' => 'Keyboard Mekanikal TKL',
                'description' => 'Keyboard mechanical blue switch layout TKL (Tenkeyless) dengan respon taktil presisi dan awet.',
                'purchase_price' => 280000,
                'selling_price' => 375000,
                'stock' => 15,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $electronics?->id ?? 1,
                'sku' => 'ELK-003',
                'name' => 'USB Flashdisk Sandisk 32GB',
                'description' => 'Flash drive USB 3.0 kecepatan transfer tinggi hingga 130MB/s untuk penyimpanan dokumen dan media.',
                'purchase_price' => 45000,
                'selling_price' => 65000,
                'stock' => 4, // Low stock demo!
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $stationery?->id ?? 2,
                'sku' => 'ATK-001',
                'name' => 'Kertas HVS A4 80gr PaperOne',
                'description' => 'Kertas HVS ukuran A4 1 rim isi 500 lembar dengan teknologi ProDigi Nanotechnology bebas macet.',
                'purchase_price' => 42000,
                'selling_price' => 55000,
                'stock' => 50,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $stationery?->id ?? 2,
                'sku' => 'ATK-002',
                'name' => 'Pulpen Gel Pilot G2 0.5 Black',
                'description' => 'Pulpen gel tinta hitam 0.5mm 1 pack (12 pcs) dengan aliran tinta lancar dan pegangan karet ergonomis.',
                'purchase_price' => 180000,
                'selling_price' => 220000,
                'stock' => 2, // Low stock demo!
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $office?->id ?? 3,
                'sku' => 'PKN-001',
                'name' => 'Stapler Heavy Duty Joyko HD-12N/24',
                'description' => 'Stapler jilid kapasitas hingga 240 lembar untuk kebutuhan kantor, percetakan, dan fotokopi.',
                'purchase_price' => 165000,
                'selling_price' => 210000,
                'stock' => 12,
                'minimum_stock' => 3,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
