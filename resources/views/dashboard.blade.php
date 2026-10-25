@extends('layouts.app')

@section('title', 'Dashboard Inventaris & Kasir')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Dashboard Inventaris & Penjualan</h1>
        <p class="page-header-subtitle">Ringkasan kondisi stok fisik gudang, penjualan kasir offline, dan pembelian persediaan.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            + Transaksi Kasir Baru
        </a>
        <a href="{{ route('purchases.create') }}" class="btn btn-outline">
            + Pembelian Supplier
        </a>
    </div>
</div>

{{-- KPI Metric Cards Grid --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 28px;">

    <div class="card" style="margin: 0; padding: 20px;">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Produk</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalProducts }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Jenis SKU terdaftar</div>
    </div>

    <div class="card" style="margin: 0; padding: 20px;">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Stok Fisik</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f766e; margin-top: 4px;">{{ number_format($totalStock, 0, ',', '.') }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Unit persediaan di toko</div>
    </div>

    <div class="card" style="margin: 0; padding: 20px; border-left: 4px solid #ef4444;">
        <div style="font-size: 12px; font-weight: 700; color: #dc2626; text-transform: uppercase;">Stok Menipis</div>
        <div style="font-size: 28px; font-weight: 800; color: #dc2626; margin-top: 4px;">{{ $lowStockProducts->count() }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Perlu segera di-restock</div>
    </div>

    <div class="card" style="margin: 0; padding: 20px;">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Penjualan Hari Ini</div>
        <div style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 6px;">Rp {{ number_format((float) $todaySalesTotal, 0, ',', '.') }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Transaksi kasir hari ini</div>
    </div>

    <div class="card" style="margin: 0; padding: 20px;">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Penjualan Bulan Ini</div>
        <div style="font-size: 22px; font-weight: 800; color: #0f766e; margin-top: 6px;">Rp {{ number_format((float) $monthlySalesTotal, 0, ',', '.') }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Omset penjualan kasir</div>
    </div>

    <div class="card" style="margin: 0; padding: 20px;">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Transaksi Sales</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalSales }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Nota transaksi selesai</div>
    </div>

    <div class="card" style="margin: 0; padding: 20px;">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Transaksi Purchase</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalPurchases }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Penerimaan dari supplier</div>
    </div>

    <div class="card" style="margin: 0; padding: 20px;">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Mutasi Stok</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalMovements }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Log pergerakan IN & OUT</div>
    </div>

</div>

{{-- Peringatan Stok Menipis --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Produk dengan Stok Menipis (&le; Batas Minimum)</h2>
        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-primary">+ Buat Pesanan Pembelian</a>
    </div>

    @if ($lowStockProducts->isEmpty())
        <div style="padding: 16px; background: #ecfdf5; border-radius: 6px; color: #065f46; font-size: 14px;">
            Semua produk berada dalam kondisi stok aman (di atas batas minimum stok).
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th style="text-align: center;">Stok Saat Ini</th>
                        <th style="text-align: center;">Batas Minimum</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lowStockProducts as $product)
                        <tr>
                            <td><code>{{ $product->sku }}</code></td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge badge-danger">{{ $product->stock }} unit</span>
                            </td>
                            <td style="text-align: center; color: #64748b;">
                                {{ $product->minimum_stock }} unit
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Two Columns: 5 Sales Terkini & 5 Mutasi Terkini --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">5 Penjualan Kasir Terkini</h2>
            <a href="{{ route('sales.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>

        @if ($recentSales->isEmpty())
            <div style="padding: 16px; text-align: center; color: #64748b; font-size: 13.5px;">
                Belum ada transaksi penjualan kasir.
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nota</th>
                            <th>Pelanggan</th>
                            <th style="text-align: right;">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentSales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" style="text-decoration: none; font-weight: 600; color: #0f172a;">
                                        <code>{{ $sale->sale_number }}</code>
                                    </a>
                                </td>
                                <td>{{ $sale->customer_name ?: 'Pelanggan Offline' }}</td>
                                <td style="text-align: right; font-weight: 700;">
                                    Rp {{ number_format((float) $sale->total, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($sale->status === 'completed')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-danger">Batal</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">5 Mutasi Stok Terkini</h2>
            <a href="{{ route('stock-movements.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>

        @if ($recentMovements->isEmpty())
            <div style="padding: 16px; text-align: center; color: #64748b; font-size: 13.5px;">
                Belum ada pergerakan stok.
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Produk</th>
                            <th style="text-align: center;">Tipe</th>
                            <th style="text-align: center;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentMovements as $mov)
                            <tr>
                                <td style="font-size: 12px; color: #64748b;">
                                    {{ $mov->created_at->format('d/m H:i') }}
                                </td>
                                <td>{{ $mov->product->name ?? '-' }}</td>
                                <td style="text-align: center;">
                                    @if($mov->type === 'IN')
                                        <span class="badge badge-success">MASUK (+ IN)</span>
                                    @else
                                        <span class="badge badge-danger">KELUAR (- OUT)</span>
                                    @endif
                                </td>
                                <td style="text-align: center; font-weight: 700;">
                                    {{ $mov->quantity }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

@endsection
