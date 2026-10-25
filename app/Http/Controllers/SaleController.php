<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:completed,cancelled'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $query = Sale::with('user')
            ->orderBy('id', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sold_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sold_at', '<=', $request->date_to);
        }

        $sales = $query
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();

        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:300'],
            'payment_method' => ['nullable', 'string', 'in:cash,transfer_bca,transfer_mandiri,transfer_bri,qris,debit'],
            'notes' => ['nullable', 'string', 'max:300'],

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
        ]);

        $sale = DB::transaction(function () use ($validated, $request) {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'customer_name' => !empty($validated['customer_name']) ? $validated['customer_name'] : 'Pelanggan Umum',
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'sale_number' => $this->generateSaleNumber(),
                'total' => 0,
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'payment_status' => 'paid',
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()
                    ->findOrFail($item['product_id']);

                $quantity = (int) $item['quantity'];

                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        'items' => 'Quantity penjualan harus lebih dari 0.',
                    ]);
                }

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' =>
                            "Stok produk {$product->name} tidak mencukupi. " .
                            "Stok tersedia: {$product->stock}.",
                    ]);
                }

                $unitPrice = (float) $product->selling_price;
                $subtotal = $unitPrice * $quantity;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'OUT',
                    'quantity' => $quantity,
                    'notes' => 'Kasir ' . $sale->sale_number . ' (' . ($sale->customer_name ?: 'Pelanggan') . ')',
                ]);

                $total += $subtotal;
            }

            $sale->update([
                'total' => $total,
            ]);

            return $sale;
        });

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Transaksi penjualan kasir berhasil disimpan. Nomor Nota: ' . $sale->sale_number
            )
            ->with('latest_sale_id', $sale->id);
    }

    public function show(Sale $sale)
    {
        $sale->load([
            'user',
            'items.product',
        ]);

        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load([
            'user',
            'items.product',
        ]);

        return view('sales.receipt', compact('sale'));
    }

    public function cancel(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $lockedSale = Sale::whereKey($sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSale->status !== 'completed') {
                throw ValidationException::withMessages([
                    'sale' => 'Transaksi sales ini sudah dibatalkan atau tidak dapat dibatalkan.',
                ]);
            }

            $items = $lockedSale->items()->get();

            foreach ($items as $item) {
                $product = Product::whereKey($item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $product->increment('stock', $item->quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'IN',
                    'quantity' => $item->quantity,
                    'notes' => 'Cancel Sale ' . $lockedSale->sale_number,
                ]);
            }

            $lockedSale->update([
                'status' => 'cancelled',
            ]);
        });

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Sales berhasil dibatalkan. Stok produk sudah dikembalikan.'
            );
    }

    private function generateSaleNumber(): string
    {
        $prefix = 'SALE-' . now()->format('Ymd') . '-';

        $lastSale = Sale::whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastSale
            ? ((int) substr($lastSale->sale_number, -4)) + 1
            : 1;

        return $prefix . str_pad(
            (string) $nextNumber,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}
