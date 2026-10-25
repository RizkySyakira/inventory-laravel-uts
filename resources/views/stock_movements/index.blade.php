@extends('layouts.app')

@section('title', 'Mutasi Stok Barang (Stock Movements)')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Mutasi Stok Barang (Stock Movements)</h1>
        <p class="page-header-subtitle">Catatan audit otomatis untuk setiap pergerakan stok barang masuk (Pembelian) dan keluar (Penjualan).</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('stock-reports.index') }}" class="btn btn-outline btn-sm">Lihat Laporan Stok</a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Waktu Mutasi</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th style="text-align: center;">Tipe Mutasi</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th>Petugas Terkait</th>
                    <th>Keterangan / Nomor Nota</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <td>{{ $movements->firstItem() + $loop->index }}</td>
                        <td style="font-size: 12.5px; color: #475569;">
                            {{ $movement->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td><code>{{ $movement->product->sku ?? '-' }}</code></td>
                        <td>
                            <strong style="color: #0f172a;">{{ $movement->product->name ?? 'Produk Dihapus' }}</strong>
                        </td>
                        <td style="text-align: center;">
                            @if ($movement->type === 'IN')
                                <span class="badge badge-success">+ MASUK (IN)</span>
                            @else
                                <span class="badge badge-danger">- KELUAR (OUT)</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: 700; font-size: 15px;">
                            {{ $movement->quantity }}
                        </td>
                        <td>{{ $movement->user->name ?? 'Sistem Otomatis' }}</td>
                        <td style="color: #475569; font-size: 13px;">
                            {{ $movement->notes ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada riwayat mutasi stok barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $movements->links() }}
    </div>
</div>

@endsection
