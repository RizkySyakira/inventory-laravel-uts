<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:completed,cancelled'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $query = Purchase::with(['supplier', 'user'])
            ->orderBy('id', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                        $supplierQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchased_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchased_at', '<=', $request->date_to);
        }

        $purchases = $query
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        if ($request->has('items') && is_array($request->items)) {
            $items = $request->items;
            foreach ($items as $k => $item) {
                if (isset($item['unit_price']) && !isset($item['unit_cost'])) {
                    $items[$k]['unit_cost'] = $item['unit_price'];
                }
            }
            $request->merge(['items' => $items]);
        }

        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'distinct',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
        ], [
            'supplier_id.required' => 'Supplier pemasok wajib dipilih.',
            'items.required' => 'Minimal harus ada 1 baris barang yang dibeli.',
            'items.min' => 'Minimal harus ada 1 baris barang yang dibeli.',
            'items.*.product_id.required' => 'Produk pada baris pembelian wajib dipilih.',
            'items.*.product_id.distinct' => 'Tidak boleh ada produk kembar di dalam satu transaksi pembelian.',
            'items.*.quantity.required' => 'Jumlah kuantitas barang wajib diisi.',
            'items.*.quantity.min' => 'Jumlah kuantitas barang minimal 1.',
            'items.*.unit_cost.required' => 'Harga beli satuan wajib diisi.',
            'items.*.unit_cost.min' => 'Harga beli satuan tidak boleh bernilai negatif.',
        ]);

        DB::transaction(function () use ($validated) {
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'user_id' => auth()->id(),
                'purchase_number' => $this->generatePurchaseNumber(),
                'total' => 0,
                'status' => 'completed',
                'purchased_at' => now(),
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()
                    ->findOrFail($item['product_id']);

                $quantity = (int) $item['quantity'];
                $unitCost = (float) $item['unit_cost'];

                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        'items' => 'Quantity pembelian harus lebih dari 0.',
                    ]);
                }

                $subtotal = $quantity * $unitCost;

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                $product->increment('stock', $quantity);

                $product->update([
                    'purchase_price' => $unitCost,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'IN',
                    'quantity' => $quantity,
                    'notes' => 'Purchase ' . $purchase->purchase_number,
                ]);

                $total += $subtotal;
            }

            $purchase->update([
                'total' => $total,
            ]);
        });

        return redirect()
            ->route('purchases.index')
            ->with(
                'success',
                'Transaksi pembelian berhasil disimpan, stok ditambahkan, dan stock movement tercatat.'
            );
    }

    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier',
            'user',
            'items.product',
        ]);

        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            $lockedPurchase = Purchase::whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPurchase->status !== 'completed') {
                throw ValidationException::withMessages([
                    'purchase' => 'Transaksi purchase ini sudah dibatalkan atau tidak dapat dibatalkan.',
                ]);
            }

            $items = $lockedPurchase->items()->get();

            foreach ($items as $item) {
                $product = Product::whereKey($item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'purchase' =>
                            "Purchase tidak dapat dibatalkan karena stok produk {$product->name} " .
                            "saat ini hanya {$product->stock}, sedangkan quantity purchase adalah {$item->quantity}.",
                    ]);
                }
            }

            foreach ($items as $item) {
                $product = Product::whereKey($item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $product->decrement('stock', $item->quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'OUT',
                    'quantity' => $item->quantity,
                    'notes' => 'Cancel Purchase ' . $lockedPurchase->purchase_number,
                ]);
            }

            $lockedPurchase->update([
                'status' => 'cancelled',
            ]);
        });

        return redirect()
            ->route('purchases.index')
            ->with(
                'success',
                'Purchase berhasil dibatalkan. Stok produk sudah dikurangi kembali.'
            );
    }

    private function generatePurchaseNumber(): string
    {
        $prefix = 'PUR-' . now()->format('Ymd') . '-';

        $lastPurchase = Purchase::whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastPurchase
            ? ((int) substr($lastPurchase->purchase_number, -4)) + 1
            : 1;

        return $prefix . str_pad(
            (string) $nextNumber,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}