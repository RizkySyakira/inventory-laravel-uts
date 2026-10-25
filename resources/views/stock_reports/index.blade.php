@extends('layouts.app')

@section('title', 'Laporan Stok & Valuasi Persediaan')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Laporan Stok & Valuasi Persediaan</h1>
        <p class="page-header-subtitle">Analisis ketersediaan fisik barang di toko dan estimasi total nilai aset persediaan.</p>
    </div>
    <div class="page-header-actions">
        <button type="button" onclick="window.print()" class="btn btn-outline btn-sm">
            Cetak Laporan
        </button>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th style="text-align: center;">Stok Fisik</th>
                    <th style="text-align: center;">Min. Stok</th>
                    <th style="text-align: right;">Harga Pokok (Beli)</th>
                    <th style="text-align: right;">Total Nilai Aset</th>
                    <th style="text-align: center;">Status Ketersediaan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotalValue = 0;
                @endphp
                @forelse ($products as $product)
                    @php
                        $assetValue = $product->stock * (float) $product->purchase_price;
                        $grandTotalValue += $assetValue;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>
                            <strong style="color: #0f172a;">{{ $product->name }}</strong>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td style="text-align: center; font-weight: 700;">
                            {{ $product->stock }} unit
                        </td>
                        <td style="text-align: center; color: #64748b;">
                            {{ $product->minimum_stock }} unit
                        </td>
                        <td style="text-align: right;">
                            Rp {{ number_format((float) $product->purchase_price, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-weight: 600; color: #0f172a;">
                            Rp {{ number_format($assetValue, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center;">
                            @if ($product->stock === 0)
                                <span class="badge badge-danger">Stok Kosong</span>
                            @elseif ($product->stock <= $product->minimum_stock)
                                <span class="badge badge-warning">Stok Menipis</span>
                            @else
                                <span class="badge badge-success">Stok Aman</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada data produk untuk dilaporkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc;">
                    <td colspan="7" style="text-align: right; font-weight: 700; font-size: 14px; padding: 14px;">
                        Total Estimasi Nilai Aset Persediaan:
                    </td>
                    <td style="text-align: right; font-weight: 800; font-size: 16px; color: #0f766e; padding: 14px;">
                        Rp {{ number_format($grandTotalValue, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
