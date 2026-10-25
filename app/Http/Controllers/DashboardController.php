<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()?->role === 'customer') {
            return redirect()->route('customer.shop');
        }

        $totalProducts = Product::count();

        $totalStock = Product::sum('stock');

        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->get();

        $totalMovements = StockMovement::count();

        $recentMovements = StockMovement::with('product')
            ->latest()
            ->take(5)
            ->get();

        $totalPurchases = Purchase::count();

        $totalSales = Sale::count();

        $todaySalesTotal = Sale::whereDate('sold_at', today())
            ->where('status', 'completed')
            ->sum('total');

        $monthlySalesTotal = Sale::whereYear('sold_at', now()->year)
            ->whereMonth('sold_at', now()->month)
            ->where('status', 'completed')
            ->sum('total');

        $recentSales = Sale::with('user')
            ->latest('sold_at')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalStock',
            'lowStockProducts',
            'totalMovements',
            'recentMovements',
            'totalPurchases',
            'totalSales',
            'todaySalesTotal',
            'monthlySalesTotal',
            'recentSales'
        ));
    }
}