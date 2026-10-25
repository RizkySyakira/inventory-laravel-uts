<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Maju Jaya Abadi',
                'contact_person' => 'Budi Santoso',
                'phone' => '081234567890',
                'email' => 'contact@majujaya.co.id',
                'address' => 'Jl. Industri Raya No. 45, Jakarta Pusat',
            ],
            [
                'name' => 'CV Sumber Makmur',
                'contact_person' => 'Siti Rahma',
                'phone' => '085678901234',
                'email' => 'sales@sumbermakmur.com',
                'address' => 'Jl. Sudirman No. 12, Surabaya',
            ],
            [
                'name' => 'Toko Berkah Stationery',
                'contact_person' => 'Ahmad Fauzi',
                'phone' => '087812345678',
                'email' => 'berkah.stationery@gmail.com',
                'address' => 'Jl. Pemuda No. 88, Semarang',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }
    }
}
