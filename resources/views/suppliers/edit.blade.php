@extends('layouts.app')

@section('title', 'Edit Supplier: ' . $supplier->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Edit Supplier</h1>
        <p class="page-header-subtitle">Perbarui data kontak atau alamat supplier <strong>{{ $supplier->name }}</strong>.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Supplier</a>
    </div>
</div>

<div class="card" style="max-width: 680px; margin: 0 auto;">
    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="form-label">Nama Perusahaan / Supplier *</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name', $supplier->name) }}"
                   required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="contact_name" class="form-label">Nama Kontak Person</label>
                <input type="text"
                       name="contact_name"
                       id="contact_name"
                       class="form-control"
                       value="{{ old('contact_name', $supplier->contact_name) }}">
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Nomor Telepon / WA</label>
                <input type="text"
                       name="phone"
                       id="phone"
                       class="form-control"
                       value="{{ old('phone', $supplier->phone) }}">
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="form-label">Alamat Kantor / Gudang Supplier</label>
            <textarea name="address"
                      id="address"
                      class="form-control"
                      rows="3">{{ old('address', $supplier->address) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection
