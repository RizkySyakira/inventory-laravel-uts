@extends('layouts.app')

@section('title', 'Edit Kategori: ' . $category->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Edit Kategori Produk</h1>
        <p class="page-header-subtitle">Perbarui data nama dan deskripsi kategori <strong>{{ $category->name }}</strong>.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('categories.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Kategori</a>
    </div>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="form-label">Nama Kategori *</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name', $category->name) }}"
                   required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Deskripsi Kategori</label>
            <textarea name="description"
                      id="description"
                      class="form-control"
                      rows="3">{{ old('description', $category->description) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
            <a href="{{ route('categories.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection
