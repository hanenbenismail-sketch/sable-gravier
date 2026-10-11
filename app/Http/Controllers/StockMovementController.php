<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Site;
use App\Models\Product;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with([
            'stock.site',
            'stock.product',
            'fromSite',
            'toSite',
            'user',
        ]);

        // Filtre par site
        if ($request->filled('site_id')) {
            $siteId = $request->site_id;

            $query->where(function ($q) use ($siteId) {
                $q->whereHas('stock', function ($stockQuery) use ($siteId) {
                    $stockQuery->where('site_id', $siteId);
                })
                ->orWhere('from_site_id', $siteId)
                ->orWhere('to_site_id', $siteId);
            });
        }

        // Filtre par produit
        if ($request->filled('product_id')) {
            $query->whereHas('stock', function ($stockQuery) use ($request) {
                $stockQuery->where('product_id', $request->product_id);
            });
        }

        // Filtre par type
        if (
            $request->filled('type') &&
            in_array($request->type, ['IN', 'OUT'], true)
        ) {
            $query->where('type', $request->type);
        }

        // Date de début
        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        // Date de fin
        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        // Pagination : 20 mouvements par page
        $movements = $query
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $sites = Site::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('stock-movements.index', compact(
            'movements',
            'sites',
            'products'
        ));
    }
}
