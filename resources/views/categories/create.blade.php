@extends('layouts.app')

@section('title', 'Tambah Kategori Produk')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Tambah Kategori Produk</h1>
        <p class="page-header-subtitle">Buat kelompok kategori baru untuk produk inventaris.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('categories.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Kategori</a>
    </div>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nama Kategori *</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name') }}"
                   placeholder="Contoh: Alat Tulis Kantor, Aksesoris Komputer..."
                   required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Deskripsi Kategori</label>
            <textarea name="description"
                      id="description"
                      class="form-control"
                      rows="3"
                      placeholder="Jelaskan jenis produk dalam kelompok kategori ini...">{{ old('description') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
            <a href="{{ route('categories.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">
                Simpan Kategori
            </button>
        </div>
    </form>
</div>

@endsection
