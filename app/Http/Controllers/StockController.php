<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Site;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with(['site', 'product'])
            ->orderBy('site_id')
            ->orderBy('product_id')
            ->get();

        return view('stocks.index', compact('stocks'));
    }

    public function create()
    {
        $sites = Site::where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('stocks.create', compact('sites', 'products'));
    }

    public function store(Request $request, StockService $stockService)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'note' => 'nullable|string|max:255',
        ]);

        $stockService->addStock(
            (int) $validated['site_id'],
            (int) $validated['product_id'],
            (float) $validated['quantity'],
            'manual',
            null,
            null,
            $validated['note'] ?? null
        );

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Stock ajouté avec succès.');
    }

    public function remove()
    {
        $sites = Site::where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('stocks.remove', compact('sites', 'products'));
    }

    public function destroy(Request $request, StockService $stockService)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            $stockService->removeStock(
                (int) $validated['site_id'],
                (int) $validated['product_id'],
                (float) $validated['quantity'],
                'manual',
                null,
                null,
                $validated['note'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'quantity' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Stock retiré avec succès.');
    }
}
