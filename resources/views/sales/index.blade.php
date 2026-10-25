@extends('layouts.app')

@section('title', 'Data Penjualan')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Data Penjualan</h1>
        <p class="page-header-subtitle">Kelola riwayat transaksi penjualan, informasi pembayaran pelanggan, dan cetak struk.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            + Transaksi Penjualan Baru
        </a>
    </div>
</div>

{{-- Banner jika baru saja menyimpan transaksi kasir --}}
@if(session('latest_sale_id'))
    <div class="alert alert-success" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div>
            <strong>Transaksi Berhasil Disimpan.</strong> Silakan cetak struk fisik untuk diserahkan ke pelanggan.
        </div>
        <a href="{{ route('sales.receipt', session('latest_sale_id')) }}" class="btn btn-sm" style="background: #0f766e; color: #ffffff;" target="_blank">
            Cetak Struk Transaksi Ini
        </a>
    </div>
@endif

<div class="card">
    <form method="GET" action="{{ route('sales.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px;">
        <div style="flex: 2; min-width: 200px;">
            <label for="search" class="form-label">Cari Nota / Pelanggan / Kasir</label>
            <input type="text"
                   id="search"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Contoh: SALE-20261025-0001, Ahmad, Budi...">
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label for="date_from" class="form-label">Dari Tanggal</label>
            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label for="date_to" class="form-label">Sampai Tanggal</label>
            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                <a href="{{ route('sales.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nomor Nota</th>
                    <th>Pelanggan</th>
                    <th>Petugas Kasir</th>
                    <th>Metode Bayar</th>
                    <th style="text-align: right;">Total Transaksi</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th style="text-align: center; width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $sales->firstItem() + $loop->index }}</td>
                        <td>
                            <strong style="font-family: monospace; color: #0f172a;">{{ $sale->sale_number }}</strong>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ $sale->customer_name ?: 'Pelanggan Toko' }}</div>
                            @if($sale->customer_phone)
                                <div style="font-size: 11.5px; color: #64748b;">{{ $sale->customer_phone }}</div>
                            @endif
                        </td>
                        <td>
                            {{ $sale->user->name ?? 'Kasir' }}
                        </td>
                        <td>
                            <span class="badge badge-secondary">
                                {{ strtoupper(str_replace('_', ' ', $sale->payment_method ?? 'CASH')) }}
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">
                            Rp {{ number_format((float) $sale->total, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($sale->status === 'completed')
                                <span class="badge badge-success">Selesai</span>
                            @else
                                <span class="badge badge-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td style="font-size: 12.5px; color: #475569;">
                            {{ $sale->sold_at?->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 4px;">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline btn-sm" title="Lihat Rincian">
                                    Detail
                                </a>
                                <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-primary btn-sm" title="Cetak Struk" target="_blank">
                                    Struk
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada riwayat transaksi penjualan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $sales->links() }}
    </div>
</div>

@endsection
