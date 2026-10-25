@extends('layouts.app')

@section('title', 'Detail Kategori: ' . $category->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Detail Kategori: {{ $category->name }}</h1>
        <p class="page-header-subtitle">Daftar produk yang tergabung dalam kategori ini.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('categories.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Kategori</a>
        @if (auth()->user()?->role === 'admin')
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">Edit Kategori</a>
        @endif
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; background: #f8fafc; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nama Kategori</div>
            <div style="font-size: 16px; font-weight: 700; color: #0f172a;">{{ $category->name }}</div>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Deskripsi</div>
            <div style="font-size: 14px; color: #334155;">{{ $category->description ?: '-' }}</div>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Jumlah Produk</div>
            <div><span class="badge badge-info">{{ $category->products->count() }} produk</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Produk dalam Kategori Ini</h3>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th style="text-align: right;">Harga Jual</th>
                    <th style="text-align: center;">Stok</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($category->products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td style="text-align: right; font-weight: 700;">Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}</td>
                        <td style="text-align: center;">
                            @if($product->stock <= $product->minimum_stock)
                                <span class="badge badge-danger">{{ $product->stock }} (Menipis)</span>
                            @else
                                <span class="badge badge-success">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-sm">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">
                            Belum ada produk yang didaftarkan pada kategori ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
