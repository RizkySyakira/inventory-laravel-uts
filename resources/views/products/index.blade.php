@extends('layouts.app')

@section('title', 'Data Produk & Persediaan')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Data Produk & Persediaan</h1>
        <p class="page-header-subtitle">Daftar inventaris barang, status persediaan fisik, harga jual, dan stok minimum.</p>
    </div>
    <div class="page-header-actions">
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                + Tambah Produk Baru
            </a>
        @endif
        <a href="{{ route('catalog.index') }}" class="btn btn-outline" target="_blank">
            Lihat di Katalog Publik
        </a>
    </div>
</div>

<div class="card">
    <form action="{{ route('products.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px;">
        <div style="flex: 2; min-width: 180px;">
            <label for="search" class="form-label">Cari Nama atau SKU</label>
            <input type="text"
                   id="search"
                   name="search"
                   class="form-control"
                   placeholder="Contoh: Mouse, HVS, SKU-001..."
                   value="{{ request('search') }}">
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label for="category_id" class="form-label">Kategori</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label for="sort" class="form-label">Urutkan</label>
            <select name="sort" id="sort" class="form-control">
                @foreach ([
                    'created_at' => 'Waktu Dibuat',
                    'name' => 'Nama Produk',
                    'sku' => 'Nomor SKU',
                    'selling_price' => 'Harga Jual',
                    'purchase_price' => 'Harga Beli',
                    'stock' => 'Stok Fisik'
                ] as $key => $label)
                    <option value="{{ $key }}" {{ $sort === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="width: 110px;">
            <label for="direction" class="form-label">Arah</label>
            <select name="direction" id="direction" class="form-control">
                <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>Menaik (A-Z)</option>
                <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Menurun (Z-A)</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Terapkan</button>
            @if(request()->anyFilled(['search', 'category_id', 'sort', 'direction']))
                <a href="{{ route('products.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 60px;">Gambar</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th style="text-align: right;">Harga Jual</th>
                    <th style="text-align: center;">Stok</th>
                    <th style="text-align: center;">Min. Stok</th>
                    <th style="text-align: center; width: 170px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $products->firstItem() + $loop->index }}</td>
                        <td>
                            <div style="width: 44px; height: 44px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px;">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                        </td>
                        <td>
                            <code style="font-weight: 600; color: #0f172a;">{{ $product->sku }}</code>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ $product->name }}</div>
                            <div style="font-size: 11.5px; color: #64748b;">Harga Beli: Rp {{ number_format((float) $product->purchase_price, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $product->category->name ?? '-' }}</span>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">
                            Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center;">
                            @if($product->stock <= $product->minimum_stock)
                                <span class="badge badge-danger" title="Stok di bawah atau sama dengan stok minimum!">
                                    {{ $product->stock }} (Menipis)
                                </span>
                            @else
                                <span class="badge badge-success">
                                    {{ $product->stock }}
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center; color: #64748b; font-weight: 500;">
                            {{ $product->minimum_stock }}
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 4px;">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-sm">
                                    Detail
                                </a>

                                @if (auth()->user()->role === 'admin')
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('products.destroy', $product) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')"
                                          style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada data produk dalam sistem persediaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $products->links() }}
    </div>
</div>

@endsection
