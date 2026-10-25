@extends('layouts.app')

@section('title', 'Data Pembelian Barang (Purchase)')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Data Pembelian Barang (Purchase)</h1>
        <p class="page-header-subtitle">Riwayat pengadaan stok persediaan barang dari supplier ke gudang toko.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            + Transaksi Pembelian Baru
        </a>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('purchases.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px;">
        <div style="flex: 2; min-width: 180px;">
            <label for="search" class="form-label">Cari Nomor Pembelian</label>
            <input type="text"
                   id="search"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Contoh: PUR-20261025-0001">
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
                <a href="{{ route('purchases.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nomor Purchase</th>
                    <th>Supplier</th>
                    <th>Petugas Penerima</th>
                    <th style="text-align: right;">Total Nilai</th>
                    <th>Status</th>
                    <th>Waktu Pembelian</th>
                    <th style="text-align: center; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchases->firstItem() + $loop->index }}</td>
                        <td>
                            <code style="font-weight: 700; color: #0f172a;">{{ $purchase->purchase_number }}</code>
                        </td>
                        <td>
                            <strong style="color: #0f172a;">{{ $purchase->supplier->name ?? '-' }}</strong>
                        </td>
                        <td>{{ $purchase->user->name ?? '-' }}</td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">
                            Rp {{ number_format((float) $purchase->total, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($purchase->status === 'completed')
                                <span class="badge badge-success">Selesai (Masuk)</span>
                            @else
                                <span class="badge badge-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td style="font-size: 12.5px; color: #475569;">
                            {{ $purchase->purchased_at?->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada data transaksi pembelian dari supplier.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $purchases->links() }}
    </div>
</div>

@endsection
