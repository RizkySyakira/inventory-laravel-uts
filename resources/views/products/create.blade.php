@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Tambah Produk Baru</h1>
        <p class="page-header-subtitle">Masukkan rincian spesifikasi produk, harga beli/jual, stok awal, dan gambar produk.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Produk</a>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="sku" class="form-label">Nomor SKU (Kode Unik) *</label>
                <input type="text"
                       name="sku"
                       id="sku"
                       class="form-control"
                       value="{{ old('sku') }}"
                       placeholder="Contoh: MOUSE-W-01"
                       maxlength="50"
                       required>
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Kategori Produk *</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap Produk *</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name') }}"
                   placeholder="Contoh: Logitech Wireless Mouse M185"
                   maxlength="150"
                   required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Deskripsi & Spesifikasi Produk</label>
            <textarea name="description"
                      id="description"
                      class="form-control"
                      rows="3"
                      placeholder="Jelaskan spesifikasi, kegunaan, dan keunggulan barang...">{{ old('description') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="purchase_price" class="form-label">Harga Beli / Pokok (Rp) *</label>
                <input type="number"
                       name="purchase_price"
                       id="purchase_price"
                       class="form-control"
                       min="0"
                       step="1"
                       value="{{ old('purchase_price', '0') }}"
                       required>
            </div>

            <div class="form-group">
                <label for="selling_price" class="form-label">Harga Jual Kasir (Rp) *</label>
                <input type="number"
                       name="selling_price"
                       id="selling_price"
                       class="form-control"
                       min="0"
                       step="1"
                       value="{{ old('selling_price', '0') }}"
                       required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="stock" class="form-label">Stok Awal Fisik *</label>
                <input type="number"
                       name="stock"
                       id="stock"
                       class="form-control"
                       min="0"
                       step="1"
                       value="{{ old('stock', '0') }}"
                       required>
            </div>

            <div class="form-group">
                <label for="minimum_stock" class="form-label">Batas Minimum Stok *</label>
                <input type="number"
                       name="minimum_stock"
                       id="minimum_stock"
                       class="form-control"
                       min="0"
                       step="1"
                       value="{{ old('minimum_stock', '5') }}"
                       required>
            </div>
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Foto / Gambar Produk</label>
            <input type="file"
                   name="image"
                   id="image"
                   class="form-control"
                   accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml">
            <div class="form-text">Format: JPG, PNG, WEBP, atau SVG. Ukuran maks: 2 MB. Jika tidak diunggah, akan memakai ilustrasi standar.</div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
            <a href="{{ route('products.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">
                Simpan Produk Baru
            </button>
        </div>
    </form>
</div>

@endsection
