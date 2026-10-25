@extends('layouts.app')

@section('title', 'Transaksi Pembelian Barang (Purchase)')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Transaksi Pembelian Barang Baru</h1>
        <p class="page-header-subtitle">Catat penerimaan barang masuk dari supplier untuk menambah stok persediaan secara otomatis.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('purchases.index') }}" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Pembelian</a>
    </div>
</div>

<form action="{{ route('purchases.store') }}" method="POST">
    @csrf

    <div class="card" style="margin-bottom: 24px;">
        <h3 class="card-title" style="margin-bottom: 16px;">Pemasok (Supplier)</h3>

        <div style="max-width: 500px;">
            <div class="form-group">
                <label for="supplier_id" class="form-label">Pilih Supplier Pemasok *</label>
                <select id="supplier_id" name="supplier_id" class="form-control" required>
                    <option value="">-- Pilih Supplier --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Daftar Barang yang Diterima</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin: 2px 0 0 0;">Stok fisik akan bertambah secara otomatis sesuai jumlah yang dicatat.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="addItem()">
                + Tambah Baris Barang
            </button>
        </div>

        <div class="table-responsive" style="margin-bottom: 20px;">
            <table>
                <thead>
                    <tr>
                        <th style="min-width: 250px;">Produk</th>
                        <th style="width: 160px; text-align: right;">Harga Beli Satuan (Rp)</th>
                        <th style="width: 120px; text-align: center;">Jumlah Masuk</th>
                        <th style="width: 160px; text-align: right;">Subtotal</th>
                        <th style="width: 60px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                </tbody>
            </table>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 14px; color: var(--text-muted); font-weight: 500;">
                Total Nilai Pembelian:
            </div>
            <div id="grand-total-display" style="font-size: 22px; font-weight: 800; color: #0f766e;">
                Rp 0
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary btn-lg">
                Simpan Transaksi Pembelian
            </button>
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
            'purchase_price' => (float) $p->purchase_price,
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
            options += `<option value="${p.id}" data-price="${p.purchase_price}">${p.sku} - ${p.name}</option>`;
        });

        row.innerHTML = `
            <td>
                <select name="items[${itemIndex}][product_id]" class="form-control" onchange="onProductChange(this, ${itemIndex})" required>
                    ${options}
                </select>
            </td>
            <td>
                <input type="number"
                       name="items[${itemIndex}][unit_price]"
                       id="price-${itemIndex}"
                       class="form-control"
                       style="text-align: right;"
                       min="0"
                       step="1"
                       value="0"
                       oninput="calculateSubtotal(${itemIndex})"
                       required>
            </td>
            <td>
                <input type="number"
                       name="items[${itemIndex}][quantity]"
                       id="qty-${itemIndex}"
                       class="form-control"
                       style="text-align: center;"
                       min="1"
                       step="1"
                       value="1"
                       oninput="calculateSubtotal(${itemIndex})"
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
        const priceInput = document.getElementById(`price-${index}`);

        if (product && priceInput) {
            priceInput.value = product.purchase_price;
        }
        calculateSubtotal(index);
    }

    function calculateSubtotal(index) {
        const priceInput = document.getElementById(`price-${index}`);
        const qtyInput = document.getElementById(`qty-${index}`);
        const subtotalEl = document.getElementById(`subtotal-${index}`);

        const price = parseFloat(priceInput?.value) || 0;
        const qty = parseInt(qtyInput?.value) || 0;
        const subtotal = price * qty;

        if (subtotalEl) {
            subtotalEl.textContent = 'Rp ' + Number(subtotal).toLocaleString('id-ID');
        }
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
            const priceInput = row.querySelector('input[name*="[unit_price]"]');
            const qtyInput = row.querySelector('input[name*="[quantity]"]');

            if (priceInput && qtyInput) {
                const price = parseFloat(priceInput.value) || 0;
                const qty = parseInt(qtyInput.value) || 0;
                total += (price * qty);
            }
        });

        document.getElementById('grand-total-display').textContent =
            'Rp ' + Number(total).toLocaleString('id-ID');
    }

    addItem();
</script>
@endsection
