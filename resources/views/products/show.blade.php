@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Detail Produk</h1>
        <p class="page-header-subtitle">Rincian spesifikasi, harga beli/jual, dan status persediaan di gudang toko.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Produk</a>
        @if (auth()->user()?->role === 'admin')
            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit Produk</a>
        @endif
        <a href="{{ route('catalog.show', $product) }}" class="btn btn-primary btn-sm" target="_blank">Lihat di Katalog</a>
    </div>
</div>

<div class="card" style="max-width: 860px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: 240px 1fr; gap: 28px; align-items: start;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; display: flex; align-items: center; justify-content: center; min-height: 220px;">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 200px; object-fit: contain;">
        </div>

        <div>
            <div style="font-size: 12px; font-weight: 700; color: #0f766e; text-transform: uppercase; margin-bottom: 4px;">
                {{ $product->category->name ?? 'Tanpa Kategori' }}
            </div>
            <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">{{ $product->name }}</h2>

            <div class="table-responsive">
                <table>
                    <tbody>
                        <tr>
                            <td style="width: 35%; color: #64748b; font-weight: 600;">Nomor SKU</td>
                            <td><code>{{ $product->sku }}</code></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Deskripsi</td>
                            <td>{{ $product->description ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Harga Beli / Pokok</td>
                            <td style="font-weight: 600;">Rp {{ number_format((float) $product->purchase_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Harga Jual Kasir</td>
                            <td style="font-weight: 700; color: #0f766e; font-size: 16px;">
                                Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Stok Fisik Gudang</td>
                            <td>
                                @if($product->stock <= $product->minimum_stock)
                                    <span class="badge badge-danger">{{ $product->stock }} (Di Bawah Batas Minimum)</span>
                                @else
                                    <span class="badge badge-success">{{ $product->stock }} (Tersedia)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Batas Minimum Stok</td>
                            <td>{{ $product->minimum_stock }} unit</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Waktu Ditambahkan</td>
                            <td>{{ $product->created_at->format('d/m/Y H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Terakhir Diperbarui</td>
                            <td>{{ $product->updated_at->format('d/m/Y H:i') }} WIB</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
