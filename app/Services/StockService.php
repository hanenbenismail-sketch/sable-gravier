<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    public function addStock(
        int $siteId,
        int $productId,
        float $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null,
        ?string $note = null
    ): Stock {
        if ($quantity <= 0) {
            throw new InvalidArgumentException(
                'La quantité doit être supérieure à zéro.'
            );
        }

        return DB::transaction(function () use (
            $siteId,
            $productId,
            $quantity,
            $referenceType,
            $referenceId,
            $userId,
            $note
        ) {
            $stock = Stock::firstOrCreate(
                [
                    'site_id' => $siteId,
                    'product_id' => $productId,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'available_quantity' => 0,
                    'last_updated' => now(),
                ]
            );

            $stock = Stock::lockForUpdate()->findOrFail($stock->id);

            $stock->quantity += $quantity;
            $stock->available_quantity =
                $stock->quantity - $stock->reserved_quantity;
            $stock->last_updated = now();
            $stock->save();

            StockMovement::create([
                'stock_id' => $stock->id,
                'type' => 'IN',
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'to_site_id' => $siteId,
                'movement_date' => now(),
                'user_id' => $userId,
                'note' => $note,
            ]);

            return $stock->fresh();
        });
    }

    public function removeStock(
        int $siteId,
        int $productId,
        float $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null,
        ?string $note = null
    ): Stock {
        if ($quantity <= 0) {
            throw new InvalidArgumentException(
                'La quantité doit être supérieure à zéro.'
            );
        }

        return DB::transaction(function () use (
            $siteId,
            $productId,
            $quantity,
            $referenceType,
            $referenceId,
            $userId,
            $note
        ) {
            $stock = Stock::where('site_id', $siteId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            $available = (float) $stock->available_quantity;

            if ($quantity > $available) {
                throw new InvalidArgumentException(
                    'Stock insuffisant. Quantité disponible : ' . $available
                );
            }

            $stock->quantity -= $quantity;
            $stock->available_quantity =
                $stock->quantity - $stock->reserved_quantity;
            $stock->last_updated = now();
            $stock->save();

            StockMovement::create([
                'stock_id' => $stock->id,
                'type' => 'OUT',
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'from_site_id' => $siteId,
                'movement_date' => now(),
                'user_id' => $userId,
                'note' => $note,
            ]);

            return $stock->fresh();
        });
    }
}