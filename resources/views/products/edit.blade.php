@extends('layouts.app')

@section('title', 'Edit Produk: ' . $product->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Edit Produk</h1>
        <p class="page-header-subtitle">Perbarui data spesifikasi, harga jual/beli, atau gambar produk <strong>{{ $product->name }}</strong>.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Produk</a>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="sku" class="form-label">Nomor SKU *</label>
                <input type="text"
                       name="sku"
                       id="sku"
                       class="form-control"
                       value="{{ old('sku', $product->sku) }}"
                       maxlength="50"
                       required>
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Kategori *</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="form-label">Nama Produk *</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name', $product->name) }}"
                   maxlength="150"
                   required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description"
                      id="description"
                      class="form-control"
                      rows="3">{{ old('description', $product->description) }}</textarea>
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
                       value="{{ old('purchase_price', $product->purchase_price) }}"
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
                       value="{{ old('selling_price', $product->selling_price) }}"
                       required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Stok Fisik Saat Ini (Terkunci)</label>
                <input type="number"
                       class="form-control"
                       value="{{ $product->stock }}"
                       disabled
                       style="background: #f1f5f9; color: #475569;">
                <div class="form-text">Perubahan stok hanya dapat dilakukan melalui transaksi Pembelian atau Kasir.</div>
            </div>

            <div class="form-group">
                <label for="minimum_stock" class="form-label">Batas Minimum Stok *</label>
                <input type="number"
                       name="minimum_stock"
                       id="minimum_stock"
                       class="form-control"
                       min="0"
                       step="1"
                       value="{{ old('minimum_stock', $product->minimum_stock) }}"
                       required>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
            <a href="{{ route('products.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection
