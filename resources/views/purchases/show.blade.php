@extends('layouts.app')

@section('title', 'Detail Pembelian: ' . $purchase->purchase_number)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Detail Transaksi Pembelian</h1>
        <p class="page-header-subtitle">Nomor Purchase: {{ $purchase->purchase_number }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('purchases.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Pembelian</a>
    </div>
</div>

<div class="card">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nomor Pembelian</div>
            <div style="font-weight: 700; font-family: monospace; font-size: 15px; color: #0f172a;">{{ $purchase->purchase_number }}</div>
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Supplier Pemasok</div>
            <div style="font-weight: 600; color: #0f172a;">{{ $purchase->supplier->name ?? '-' }}</div>
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Petugas Penerima</div>
            <div style="font-weight: 600; color: #0f172a;">{{ $purchase->user->name ?? '-' }}</div>
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Status Transaksi</div>
            <div>
                @if($purchase->status === 'completed')
                    <span class="badge badge-success">Selesai (Stok Masuk)</span>
                @else
                    <span class="badge badge-danger">Dibatalkan</span>
                @endif
            </div>
        </div>

        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Waktu Pembelian</div>
            <div style="font-weight: 600; color: #0f172a;">{{ $purchase->purchased_at?->format('d/m/Y H:i') ?? '-' }} WIB</div>
        </div>
    </div>

    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Daftar Barang Masuk</h3>

    <div class="table-responsive" style="margin-bottom: 20px;">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th style="text-align: right;">Harga Beli Satuan</th>
                    <th style="text-align: center;">Jumlah Masuk</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><code>{{ $item->product->sku ?? '-' }}</code></td>
                        <td><strong>{{ $item->product->name ?? 'Produk Dihapus' }}</strong></td>
                        <td style="text-align: right;">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: 700;">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: 700; font-size: 15px; padding-top: 14px;">Total Nilai Pembelian:</td>
                    <td style="text-align: right; font-weight: 800; font-size: 18px; color: #0f766e; padding-top: 14px;">
                        Rp {{ number_format((float) $purchase->total, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('purchases.index') }}" class="btn btn-outline">
            &larr; Kembali ke Daftar Pembelian
        </a>

        @if(auth()->user()->role === 'admin' && $purchase->status === 'completed')
            <form action="{{ route('purchases.cancel', $purchase) }}"
                  method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi pembelian ini? Stok barang akan dikurangi kembali.');"
                  style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-danger">
                    Batalkan Pembelian (Kurangi Stok)
                </button>
            </form>
        @endif
    </div>
</div>

@endsection
