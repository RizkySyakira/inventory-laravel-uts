<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function receipt(string $saleNumber)
    {
        $sale = Sale::with(['items.product', 'user'])
            ->where('sale_number', $saleNumber)
            ->firstOrFail();

        $paymentDetails = $this->getPaymentDetails($sale->payment_method);

        return view('customer.receipt', compact('sale', 'paymentDetails'));
    }

    public function track(Request $request)
    {
        $sales = null;

        if ($request->filled('query')) {
            $q = trim($request->input('query'));

            $sales = Sale::with(['items.product'])
                ->where('sale_number', $q)
                ->orWhere('customer_phone', $q)
                ->latest('sold_at')
                ->take(10)
                ->get();
        }

        return view('customer.track', compact('sales'));
    }

    private function getPaymentDetails(string $method): array
    {
        return match ($method) {
            'transfer_bca' => [
                'name' => 'Bank Central Asia (BCA)',
                'account_number' => '8210-9876-5432',
                'account_name' => 'PT Inventory Store Indonesia',
                'instructions' => 'Silakan transfer sesuai nominal total pesanan ke rekening BCA di atas.',
            ],
            'transfer_mandiri' => [
                'name' => 'Bank Mandiri',
                'account_number' => '137-00-1234567-8',
                'account_name' => 'PT Inventory Store Indonesia',
                'instructions' => 'Silakan transfer sesuai nominal total pesanan ke rekening Mandiri di atas.',
            ],
            'transfer_bri' => [
                'name' => 'Bank BRI',
                'account_number' => '0123-01-001234-50-8',
                'account_name' => 'PT Inventory Store Indonesia',
                'instructions' => 'Silakan transfer sesuai nominal total pesanan ke rekening BRI di atas.',
            ],
            'qris' => [
                'name' => 'QRIS (GoPay, OVO, Dana, ShopeePay)',
                'account_number' => 'NMID: ID1020030040050',
                'account_name' => 'Inventory Store QRIS Resmi',
                'instructions' => 'Pindai (scan) kode QRIS di bawah menggunakan aplikasi e-wallet Anda.',
            ],
            'cod' => [
                'name' => 'Cash On Delivery (COD)',
                'account_number' => '-',
                'account_name' => 'Bayar di Tempat saat Barang Tiba',
                'instructions' => 'Siapkan uang pas saat kurir mengantarkan barang pesanan Anda.',
            ],
            'cash' => [
                'name' => 'Tunai di Kasir Toko',
                'account_number' => '-',
                'account_name' => 'Kasir Offline Toko',
                'instructions' => 'Tunjukkan struk ini kepada petugas kasir kami di toko.',
            ],
            default => [
                'name' => 'Pembayaran Langsung',
                'account_number' => '-',
                'account_name' => 'Toko',
                'instructions' => 'Selesaikan pembayaran di kasir.',
            ],
        };
    }
}
