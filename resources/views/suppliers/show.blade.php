@extends('layouts.app')

@section('title', 'Detail Supplier: ' . $supplier->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Detail Supplier</h1>
        <p class="page-header-subtitle">{{ $supplier->name }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Supplier</a>
        @if (auth()->user()?->role === 'admin')
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm">Edit Supplier</a>
        @endif
    </div>
</div>

<div class="card" style="max-width: 720px; margin: 0 auto 24px auto;">
    <div class="table-responsive">
        <table>
            <tbody>
                <tr>
                    <td style="width: 35%; color: #64748b; font-weight: 600;">Nama Perusahaan</td>
                    <td><strong style="color: #0f172a; font-size: 15px;">{{ $supplier->name }}</strong></td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600;">Kontak Person</td>
                    <td>{{ $supplier->contact_name ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600;">Nomor Telepon / WA</td>
                    <td><code>{{ $supplier->phone ?: '-' }}</code></td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600;">Alamat Kantor / Gudang</td>
                    <td>{{ $supplier->address ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600;">Waktu Didaftarkan</td>
                    <td>{{ $supplier->created_at->format('d/m/Y H:i') }} WIB</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
