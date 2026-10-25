@extends('layouts.app')

@section('title', $product->name . ' - Katalog Produk')

@section('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 32px;
        align-items: start;
        margin-bottom: 32px;
    }

    .detail-image-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 380px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    .detail-image {
        max-width: 100%;
        max-height: 320px;
        object-fit: contain;
    }

    .detail-info-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    .detail-category {
        font-size: 13px;
        font-weight: 700;
        color: #0f766e;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
    }

    .detail-title {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .detail-price-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: baseline;
        justify-content: space-between;
    }

    .detail-price-val {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    .detail-specs-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .detail-specs-table td {
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-specs-table td:first-child {
        color: #64748b;
        font-weight: 500;
        width: 35%;
    }

    .detail-specs-table td:last-child {
        color: #0f172a;
        font-weight: 600;
    }

    .offline-instruction-box {
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        border-radius: 10px;
        padding: 20px;
        margin-top: 24px;
    }

    .instruction-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f766e;
        margin-bottom: 10px;
    }

    .instruction-list {
        margin: 0;
        padding-left: 18px;
        font-size: 13.5px;
        color: #134e4a;
        line-height: 1.6;
    }

    .instruction-list li {
        margin-bottom: 6px;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 18px;
        margin-top: 16px;
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

    <div style="margin-bottom: 18px;">
        <a href="{{ route('catalog.index') }}" class="btn btn-outline btn-sm">
            &larr; Kembali ke Katalog
        </a>
    </div>

    <div class="detail-grid">
        {{-- Product Image --}}
        <div class="detail-image-card">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="detail-image">
        </div>

        {{-- Product Information --}}
        <div class="detail-info-card">
            <div class="detail-category">{{ $product->category->name ?? 'Umum' }}</div>
            <h1 class="detail-title">{{ $product->name }}</h1>

            <div class="detail-price-box">
                <div>
                    <span style="font-size: 12px; color: #64748b; display: block; font-weight: 500;">Harga Satuan</span>
                    <span class="detail-price-val">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                </div>
                <div>
                    @if($product->stock > 10)
                        <span class="badge badge-success" style="font-size: 13px; padding: 6px 12px;">Stok Tersedia ({{ $product->stock }})</span>
                    @elseif($product->stock > 0)
                        <span class="badge badge-warning" style="font-size: 13px; padding: 6px 12px;">Stok Terbatas ({{ $product->stock }})</span>
                    @else
                        <span class="badge badge-danger" style="font-size: 13px; padding: 6px 12px;">Stok Kosong</span>
                    @endif
                </div>
            </div>

            <table class="detail-specs-table">
                <tr>
                    <td>Nomor SKU</td>
                    <td><code>{{ $product->sku }}</code></td>
                </tr>
                <tr>
                    <td>Kategori</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Ketersediaan Stok</td>
                    <td>{{ $product->stock > 0 ? $product->stock . ' unit siap di toko' : 'Stok habis' }}</td>
                </tr>
            </table>

            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Deskripsi Produk</h3>
                <p style="font-size: 14px; color: #475569; line-height: 1.6;">
                    {{ $product->description ?: 'Produk perlengkapan kantor dan bisnis dengan spesifikasi standar teruji, siap digunakan untuk mendukung produktivitas kerja harian.' }}
                </p>
            </div>

            {{-- Purchase Guide --}}
            <div class="offline-instruction-box">
                <div class="instruction-title">Petunjuk Pembelian di Toko</div>
                <ol class="instruction-list">
                    <li>Kunjungi lokasi toko kami di <strong>Jl. Jenderal Sudirman No. 123, Jakarta Pusat</strong> (Buka Senin &ndash; Sabtu, 08.00 &ndash; 21.00 WIB).</li>
                    <li>Sebutkan nama barang atau kode SKU <strong>{{ $product->sku }}</strong> kepada staf kasir.</li>
                    <li>Pembayaran dapat dilakukan melalui <strong>Tunai, QRIS, Transfer BCA/Mandiri/BRI, atau Kartu Debit</strong>.</li>
                    <li>Struk resmi transaksi akan dicetak langsung oleh kasir setelah pembayaran selesai.</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Related Products --}}
    @if($relatedProducts->isNotEmpty())
        <div class="card" style="margin-top: 32px;">
            <div class="card-header">
                <h2 class="card-title">Produk Terkait Dalam Kategori {{ $product->category->name ?? '' }}</h2>
                <a href="{{ route('catalog.index', ['category_id' => $product->category_id]) }}" class="btn btn-outline btn-sm">Lihat Semua Kategori Ini</a>
            </div>

            <div class="related-grid">
                @foreach($relatedProducts as $rel)
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; background: #ffffff;">
                        <div style="height: 120px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 6px; margin-bottom: 10px; padding: 10px;">
                            <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        </div>
                        <div style="font-size: 11px; color: #0f766e; font-weight: 700; margin-bottom: 4px;">{{ $rel->sku }}</div>
                        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 6px;">
                            <a href="{{ route('catalog.show', $rel->id) }}" style="text-decoration: none; color: #0f172a;">{{ $rel->name }}</a>
                        </h4>
                        <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">
                            Rp {{ number_format($rel->selling_price, 0, ',', '.') }}
                        </div>
                        <a href="{{ route('catalog.show', $rel->id) }}" class="btn btn-outline btn-sm" style="width: 100%;">Lihat Detail</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection
