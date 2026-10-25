@extends('layouts.app')

@section('title', 'Struk Pembayaran: ' . $sale->sale_number)

@section('styles')
<style>
    .receipt-wrapper {
        max-width: 680px;
        margin: 0 auto;
    }

    .receipt-actions-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .receipt-paper {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 36px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        color: #0f172a;
    }

    .receipt-header {
        text-align: center;
        border-bottom: 2px dashed #cbd5e1;
        padding-bottom: 20px;
        margin-bottom: 24px;
    }

    .receipt-store-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
        letter-spacing: 0.02em;
    }

    .receipt-store-subtitle {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 2px;
    }

    .receipt-badge-title {
        display: inline-block;
        margin-top: 14px;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 4px 12px;
        background: #f1f5f9;
        border-radius: 4px;
    }

    .receipt-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
        font-size: 13px;
    }

    .receipt-meta-grid div {
        line-height: 1.5;
    }

    .receipt-meta-label {
        color: #64748b;
        font-size: 11.5px;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.03em;
    }

    .receipt-meta-value {
        font-weight: 700;
        color: #0f172a;
    }

    .receipt-items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        font-size: 13.5px;
    }

    .receipt-items-table th {
        background: #f1f5f9;
        color: #334155;
        font-weight: 700;
        padding: 10px 12px;
        border-bottom: 1px solid #cbd5e1;
        text-align: left;
    }

    .receipt-items-table td {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .receipt-summary-box {
        border-top: 2px dashed #cbd5e1;
        padding-top: 16px;
        margin-bottom: 24px;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13.5px;
    }

    .summary-line-total {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        border-top: 1px solid #e2e8f0;
        padding-top: 10px;
        margin-top: 8px;
    }

    .receipt-footer-text {
        text-align: center;
        border-top: 1px dashed #cbd5e1;
        padding-top: 20px;
        font-size: 12px;
        color: #64748b;
        line-height: 1.6;
    }

    @media print {
        .app-navbar,
        .app-footer,
        .receipt-actions-bar {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
        }

        .receipt-paper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            max-width: 100% !important;
        }
    }
</style>
@endsection

@section('content')

@php
    $methodNames = [
        'cash' => 'Tunai / Cash',
        'transfer_bca' => 'Transfer Bank BCA',
        'transfer_mandiri' => 'Transfer Bank Mandiri',
        'transfer_bri' => 'Transfer Bank BRI',
        'qris' => 'QRIS Standar Indonesia',
        'debit' => 'Kartu Debit EDC Kasir',
    ];
    $paymentMethodLabel = $methodNames[$sale->payment_method] ?? strtoupper(str_replace('_', ' ', $sale->payment_method ?? 'CASH'));
@endphp

<div class="receipt-wrapper">

    <div class="receipt-actions-bar">
        <div>
            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'staff'))
                <a href="{{ route('sales.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Penjualan</a>
            @else
                <a href="{{ route('catalog.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Katalog Toko</a>
            @endif
        </div>

        <div>
            <button type="button" onclick="window.print()" class="btn btn-primary btn-sm">
                Cetak Struk Transaksi
            </button>
        </div>
    </div>

    <div class="receipt-paper">
        <div class="receipt-header">
            <h1 class="receipt-store-title">INVENTORY STORE INDONESIA</h1>
            <p class="receipt-store-subtitle">Pusat Grosir & Eceran Perlengkapan Kantor dan Komputer</p>
            <p class="receipt-store-subtitle">Jl. Jenderal Sudirman No. 123, Jakarta Pusat | Telp: (021) 555-0199</p>
            <div class="receipt-badge-title">STRUK RESMI TRANSAKSI PENJUALAN</div>
        </div>

        <div class="receipt-meta-grid">
            <div>
                <div class="receipt-meta-label">Nomor Nota</div>
                <div class="receipt-meta-value"><code>{{ $sale->sale_number }}</code></div>

                <div class="receipt-meta-label" style="margin-top: 8px;">Waktu Transaksi</div>
                <div class="receipt-meta-value">{{ $sale->sold_at ? $sale->sold_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} WIB</div>

                <div class="receipt-meta-label" style="margin-top: 8px;">Petugas Kasir</div>
                <div class="receipt-meta-value">{{ $sale->user->name ?? 'Kasir Toko' }}</div>
            </div>

            <div>
                <div class="receipt-meta-label">Nama Pelanggan</div>
                <div class="receipt-meta-value">{{ $sale->customer_name ?: 'Pelanggan Toko' }}</div>

                <div class="receipt-meta-label" style="margin-top: 8px;">Nomor Telepon</div>
                <div class="receipt-meta-value">{{ $sale->customer_phone ?: '-' }}</div>

                <div class="receipt-meta-label" style="margin-top: 8px;">Status Transaksi</div>
                <div>
                    @if($sale->status === 'completed')
                        <span class="badge badge-success">Selesai (Lunas)</span>
                    @else
                        <span class="badge badge-danger">Dibatalkan</span>
                    @endif
                </div>
            </div>
        </div>

        <table class="receipt-items-table">
            <thead>
                <tr>
                    <th style="width: 36px;">No</th>
                    <th>Nama Barang</th>
                    <th style="text-align: right; width: 110px;">Harga Satuan</th>
                    <th style="text-align: center; width: 60px;">Qty</th>
                    <th style="text-align: right; width: 120px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                            <div style="font-size: 11px; color: #64748b;">SKU: {{ $item->product->sku ?? '-' }}</div>
                        </td>
                        <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: 700;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="receipt-summary-box">
            <div class="summary-line">
                <span style="color: #64748b;">Metode Pembayaran:</span>
                <span style="font-weight: 700; color: #0f172a;">{{ $paymentMethodLabel }}</span>
            </div>
            <div class="summary-line">
                <span style="color: #64748b;">Status Pembayaran:</span>
                <span style="font-weight: 700; color: #059669;">LUNAS DI KASIR</span>
            </div>
            @if($sale->notes)
                <div class="summary-line" style="font-size: 12.5px;">
                    <span style="color: #64748b;">Catatan Kasir:</span>
                    <span>{{ $sale->notes }}</span>
                </div>
            @endif
            <div class="summary-line summary-line-total">
                <span>TOTAL DIBAYAR:</span>
                <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="receipt-footer-text">
            <p style="font-weight: 600; margin-bottom: 4px; color: #0f172a;">Terima kasih telah berbelanja di Inventory Store Indonesia.</p>
            <p>Barang yang sudah dibeli telah dicek dalam kondisi baik dan tidak dapat ditukar kecuali terdapat perjanjian garansi tertulis.</p>
            <p style="margin-top: 6px; font-family: monospace; font-size: 11px;">KODE AUTH: {{ strtoupper(md5($sale->sale_number . $sale->created_at)) }}</p>
        </div>
    </div>

</div>

@endsection
