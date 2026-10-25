@extends('layouts.app')

@section('title', 'Detail Penjualan: ' . $sale->sale_number)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Detail Transaksi Penjualan</h1>
        <p class="page-header-subtitle">Nomor Nota: {{ $sale->sale_number }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('sales.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Penjualan</a>
        <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-primary btn-sm" target="_blank">
            Cetak Struk Resmi
        </a>
    </div>
</div>

<div class="card">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nomor Nota</div>
            <div style="font-weight: 700; font-family: monospace; font-size: 15px; color: #0f172a;">{{ $sale->sale_number }}</div>
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Petugas Kasir</div>
            <div style="font-weight: 600; color: #0f172a;">{{ $sale->user->name ?? 'Kasir Toko' }}</div>
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nama Pelanggan</div>
            <div style="font-weight: 600; color: #0f172a;">{{ $sale->customer_name ?: 'Pelanggan Toko' }}</div>
            @if($sale->customer_phone)
                <div style="font-size: 12px; color: #64748b;">{{ $sale->customer_phone }}</div>
            @endif
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Metode Pembayaran</div>
            <div style="font-weight: 600; color: #0f172a;">
                {{ strtoupper(str_replace('_', ' ', $sale->payment_method ?? 'CASH')) }}
            </div>
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Status Transaksi</div>
            <div>
                @if($sale->status === 'completed')
                    <span class="badge badge-success">Selesai (Completed)</span>
                @else
                    <span class="badge badge-danger">Dibatalkan (Cancelled)</span>
                @endif
            </div>
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Waktu Transaksi</div>
            <div style="font-weight: 600; color: #0f172a;">{{ $sale->sold_at?->format('d/m/Y H:i') ?? '-' }} WIB</div>
        </div>
    </div>

    @if($sale->notes)
        <div style="background: #f1f5f9; padding: 12px 16px; border-radius: 6px; font-size: 13.5px; margin-bottom: 24px;">
            <strong>Catatan:</strong> {{ $sale->notes }}
        </div>
    @endif

    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Daftar Barang Penjualan</h3>

    <div class="table-responsive" style="margin-bottom: 20px;">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><code>{{ $item->product->sku ?? '-' }}</code></td>
                        <td>
                            <strong>{{ $item->product->name ?? 'Produk Dihapus' }}</strong>
                        </td>
                        <td style="text-align: right;">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: 700;">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: 700; font-size: 15px; padding-top: 14px;">Total Transaksi:</td>
                    <td style="text-align: right; font-weight: 800; font-size: 18px; color: #0f766e; padding-top: 14px;">
                        Rp {{ number_format((float) $sale->total, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('sales.index') }}" class="btn btn-outline">
            &larr; Kembali ke Daftar Sales
        </a>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-primary" target="_blank">
                Cetak Struk Resmi
            </a>

            @if(auth()->user()->role === 'admin' && $sale->status === 'completed')
                <form action="{{ route('sales.cancel', $sale) }}"
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini? Stok seluruh item akan otomatis dikembalikan ke inventaris.');"
                      style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        Batalkan Transaksi (Kembalikan Stok)
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection
