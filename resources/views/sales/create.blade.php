@extends('layouts.app')

@section('title', 'Transaksi Penjualan')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Transaksi Penjualan</h1>
        <p class="page-header-subtitle">Catat transaksi penjualan, pilih metode pembayaran, dan cetak struk untuk pelanggan.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('sales.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Penjualan</a>
    </div>
</div>

<form action="{{ route('sales.store') }}" method="POST" id="sales-form">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; align-items: start;">

        {{-- Kolom Kiri: Informasi Pelanggan & Pembayaran --}}
        <div class="card">
            <h3 class="card-title" style="margin-bottom: 16px;">Informasi Transaksi</h3>

            <div class="form-group">
                <label for="customer_name" class="form-label">Nama Pelanggan</label>
                <input type="text"
                       name="customer_name"
                       id="customer_name"
                       class="form-control"
                       placeholder="Contoh: Bpk. Ahmad (atau kosongkan)"
                       value="{{ old('customer_name') }}">
                <div class="form-text">Boleh dikosongkan (default: Pelanggan Umum)</div>
            </div>

            <div class="form-group">
                <label for="customer_phone" class="form-label">Nomor Telepon / WA</label>
                <input type="text"
                       name="customer_phone"
                       id="customer_phone"
                       class="form-control"
                       placeholder="Contoh: 081234567890"
                       value="{{ old('customer_phone') }}">
                <div class="form-text">Digunakan pelanggan jika ingin mencari struk secara online</div>
            </div>

            <div class="form-group">
                <label for="customer_address" class="form-label">Alamat / Catatan Lokasi</label>
                <input type="text"
                       name="customer_address"
                       id="customer_address"
                       class="form-control"
                       placeholder="Contoh: Ambil di Toko"
                       value="{{ old('customer_address') }}">
            </div>

            <div class="form-group">
                <label for="payment_method" class="form-label">Metode Pembayaran *</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="cash" selected>Tunai / Cash</option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Catatan Tambahan</label>
                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Catatan transaksi bila ada...">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Barang Penjualan --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Daftar Produk yang Dibeli</h3>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 2px 0 0 0;">Pilih produk dan masukkan kuantitas barang.</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="addItem()">
                    + Tambah Baris Produk
                </button>
            </div>

            <div class="table-responsive" style="margin-bottom: 20px;">
                <table>
                    <thead>
                        <tr>
                            <th style="min-width: 220px;">Produk (SKU - Nama)</th>
                            <th style="width: 100px; text-align: center;">Stok</th>
                            <th style="width: 140px; text-align: right;">Harga Satuan</th>
                            <th style="width: 100px; text-align: center;">Jumlah</th>
                            <th style="width: 140px; text-align: right;">Subtotal</th>
                            <th style="width: 60px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                    </tbody>
                </table>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 14px; color: var(--text-muted); font-weight: 500;">
                    Total Estimasi Penjualan:
                </div>
                <div id="grand-total-display" style="font-size: 22px; font-weight: 800; color: #0f766e;">
                    Rp 0
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('sales.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    Simpan & Cetak Struk
                </button>
            </div>
        </div>

    </div>
</form>

@php
    $productData = [];
    foreach ($products as $p) {
        $productData[] = [
            'id' => $p->id,
            'sku' => $p->sku,
            'name' => $p->name,
            'stock' => $p->stock,
            'selling_price' => (float) $p->selling_price,
        ];
    }
@endphp

@endsection

@section('scripts')
<script>
    let itemIndex = 0;
    const products = @json($productData);

    function addItem() {
        const tbody = document.getElementById('items-body');
        const row = document.createElement('tr');
        row.id = `row-${itemIndex}`;

        let options = '<option value="">-- Pilih Produk --</option>';
        products.forEach(p => {
            options += `<option value="${p.id}" data-stock="${p.stock}" data-price="${p.selling_price}">${p.sku} - ${p.name} (Stok: ${p.stock})</option>`;
        });

        row.innerHTML = `
            <td>
                <select name="items[${itemIndex}][product_id]" class="form-control" onchange="onProductChange(this, ${itemIndex})" required>
                    ${options}
                </select>
            </td>
            <td style="text-align: center;" id="stock-${itemIndex}">
                <span class="badge badge-secondary">-</span>
            </td>
            <td style="text-align: right; font-weight: 600;" id="price-${itemIndex}">
                Rp 0
            </td>
            <td>
                <input type="number"
                       name="items[${itemIndex}][quantity]"
                       id="qty-${itemIndex}"
                       class="form-control"
                       style="text-align: center;"
                       min="1"
                       value="1"
                       oninput="onQuantityChange(${itemIndex})"
                       required>
            </td>
            <td style="text-align: right; font-weight: 700; color: #0f172a;" id="subtotal-${itemIndex}">
                Rp 0
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${itemIndex})">
                    &times;
                </button>
            </td>
        `;

        tbody.appendChild(row);
        itemIndex++;
        calculateGrandTotal();
    }

    function onProductChange(select, index) {
        const selectedId = select.value;
        const product = products.find(p => p.id == selectedId);

        const stockEl = document.getElementById(`stock-${index}`);
        const priceEl = document.getElementById(`price-${index}`);
        const qtyEl = document.getElementById(`qty-${index}`);

        if (product) {
            stockEl.innerHTML = `<span class="badge ${product.stock > 0 ? 'badge-success' : 'badge-danger'}">${product.stock}</span>`;
            priceEl.textContent = 'Rp ' + Number(product.selling_price).toLocaleString('id-ID');
            qtyEl.max = product.stock;
            if (parseInt(qtyEl.value) > product.stock) {
                qtyEl.value = product.stock > 0 ? 1 : 0;
            }
        } else {
            stockEl.innerHTML = `<span class="badge badge-secondary">-</span>`;
            priceEl.textContent = 'Rp 0';
        }

        onQuantityChange(index);
    }

    function onQuantityChange(index) {
        const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
        const qtyEl = document.getElementById(`qty-${index}`);
        const subtotalEl = document.getElementById(`subtotal-${index}`);

        if (!select || !select.value) {
            subtotalEl.textContent = 'Rp 0';
            calculateGrandTotal();
            return;
        }

        const product = products.find(p => p.id == select.value);
        const qty = parseInt(qtyEl.value) || 0;
        const subtotal = product ? (product.selling_price * qty) : 0;

        subtotalEl.textContent = 'Rp ' + Number(subtotal).toLocaleString('id-ID');
        calculateGrandTotal();
    }

    function removeItem(index) {
        const row = document.getElementById(`row-${index}`);
        if (row) {
            row.remove();
        }
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        const rows = document.querySelectorAll('#items-body tr');

        rows.forEach(row => {
            const select = row.querySelector('select');
            const qtyInput = row.querySelector('input[type="number"]');

            if (select && select.value && qtyInput) {
                const product = products.find(p => p.id == select.value);
                const qty = parseInt(qtyInput.value) || 0;
                if (product) {
                    total += (product.selling_price * qty);
                }
            }
        });

        document.getElementById('grand-total-display').textContent =
            'Rp ' + Number(total).toLocaleString('id-ID');
    }

    // Tambah item awal saat halaman dimuat
    addItem();
</script>
@endsection
