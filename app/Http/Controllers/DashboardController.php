<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Site;
use App\Models\StockMovement;

class DashboardController extends Controller
{
    public function index()
    {
        // Nombre total de sites
        $totalSites = Site::count();

        // Nombre total de produits
        $totalProducts = Product::count();

        // Nombre total de mouvements de stock
        $totalMovements = StockMovement::count();

        // Nombre des entrées de stock
        $totalEntries = StockMovement::where('type', 'IN')->count();

        // Nombre des sorties de stock
        $totalExits = StockMovement::where('type', 'OUT')->count();

        // Quantité totale en stock
        $totalStock = Stock::sum('quantity');

        // Stocks à surveiller selon le minimum défini pour chaque produit
        $lowStockCount = Stock::query()
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->whereRaw(
                '(stocks.quantity - stocks.reserved_quantity) <= products.min_stock'
            )
            ->count();

        // Les 5 derniers mouvements de stock
        $recentMovements = StockMovement::with([
            'stock.site',
            'stock.product',
        ])
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalSites',
            'totalProducts',
            'totalMovements',
            'totalEntries',
            'totalExits',
            'totalStock',
            'lowStockCount',
            'recentMovements'
        ));
    }
}
