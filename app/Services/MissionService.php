<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\MissionStop;
use App\Models\Product;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MissionService
{
    private const EPSILON = 0.0005;

    public function calculateCosts(Mission $mission): Mission
    {
        return DB::transaction(function () use ($mission) {
            $mission->loadMissing(['truck', 'driver', 'fuelRecords']);

            $fuelCost = $mission->fuelRecords->isNotEmpty()
                ? (float) $mission->fuelRecords->sum('total_amount')
                : (float) $mission->fuel_cost;

            $driver = $mission->driver;
            $distance = (float) $mission->total_distance;
            $driverCost = 0.0;

            if ($driver) {
                $driverCost = match ($driver->salary_type) {
                    'per_trip' => (float) $driver->salary_per_trip,
                    'per_km' => $distance * (float) $driver->salary_per_km,
                    'daily' => (float) $driver->salary_per_day,
                    'monthly' => (float) $driver->base_salary / 26,
                    default => 0.0,
                };

                $driverCost += (float) $driver->bonus;
            }

            $fuelCost = max($fuelCost, 0);
            $otherCost = (float) $mission->other_cost;
            $transportCost = (float) $mission->transport_cost;
            $totalCost = $fuelCost + $driverCost + $otherCost + $transportCost;

            $mission->update([
                'fuel_cost' => $fuelCost,
                'driver_cost' => $driverCost,
                'other_cost' => $otherCost,
                'transport_cost' => $transportCost,
                'total_cost' => $totalCost,
                'profit' => (float) $mission->total_revenue - $totalCost,
            ]);

            return $mission->fresh();
        });
    }

    public function startMission(Mission $mission): Mission
    {
        return DB::transaction(function () use ($mission) {
            $mission = Mission::whereKey($mission->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($mission->status !== 'planned') {
                throw new InvalidArgumentException(
                    'Seule une mission planifiée peut être démarrée.'
                );
            }

            if ($mission->departure_time !== null) {
                throw new InvalidArgumentException(
                    'L’heure de départ est déjà enregistrée.'
                );
            }

            $mission->update([
                'status' => 'in_progress',
                'departure_time' => now(),
            ]);

            return $mission->fresh();
        });
    }

    public function loadMissionStop(
        MissionStop $stop,
        float $quantity,
        ?int $userId = null
    ): MissionStop {
        return DB::transaction(function () use ($stop, $quantity, $userId) {
            $stop = MissionStop::whereKey($stop->id)
                ->lockForUpdate()
                ->firstOrFail();

            $mission = Mission::whereKey($stop->mission_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureMissionActive($mission);
            $this->ensureNoReturnRecorded($mission);

            if ($stop->stop_type !== 'loading') {
                throw new InvalidArgumentException(
                    'Cet arrêt n’est pas un arrêt de chargement.'
                );
            }

            if ($stop->loading_status !== 'pending') {
                throw new InvalidArgumentException(
                    'Seul un chargement en attente peut être effectué.'
                );
            }

            if (!$stop->site_id || !$stop->product_id) {
                throw new InvalidArgumentException(
                    'Le site et le produit sont obligatoires pour le chargement.'
                );
            }

            if (!is_finite($quantity) || $quantity <= 0) {
                throw new InvalidArgumentException(
                    'La quantité chargée doit être supérieure à zéro.'
                );
            }

            if (round($quantity, 3) !== $quantity) {
                throw new InvalidArgumentException(
                    'La quantité ne peut pas dépasser trois décimales.'
                );
            }

            $existingMovement = DB::table('stock_movements')
                ->where('reference_type', 'mission_stop')
                ->where('reference_id', $stop->id)
                ->where('type', 'OUT')
                ->exists();

            if ($existingMovement) {
                throw new InvalidArgumentException(
                    'Un mouvement de sortie existe déjà pour cet arrêt.'
                );
            }

            $truck = $mission->truck;

            if (!$truck || (float) $truck->capacity <= 0) {
                throw new InvalidArgumentException(
                    'Le camion doit avoir une capacité valide.'
                );
            }

            $product = Product::findOrFail($stop->product_id);

            $this->ensureSameUnit($product->unit, $stop->unit);
            $this->ensureSameUnit($truck->unit, $stop->unit);

            $loadedStops = MissionStop::where('mission_id', $mission->id)
                ->where('stop_type', 'loading')
                ->where('loading_status', 'loaded')
                ->where('stop_order', '<', $stop->stop_order)
                ->get();

            $currentLoad = 0.0;

            foreach ($loadedStops as $loadedStop) {
                $this->ensureSameUnit($truck->unit, $loadedStop->unit);

                $currentLoad += (float) $loadedStop->quantity;
            }

            $deliveredStops = MissionStop::where(
                'mission_id',
                $mission->id
            )
                ->whereIn('stop_type', ['delivery', 'unloading'])
                ->where('stop_order', '<', $stop->stop_order)
                ->get();

            $deliveredQuantity = 0.0;

            foreach ($deliveredStops as $deliveredStop) {
                $this->ensureSameUnit($truck->unit, $deliveredStop->unit);

                $deliveredQuantity += (float) $deliveredStop->delivered_quantity;
            }

            $currentLoad = max($currentLoad - $deliveredQuantity, 0);

            if (
                $currentLoad + $quantity
                > (float) $truck->capacity + self::EPSILON
            ) {
                throw new InvalidArgumentException(
                    'Capacité du camion dépassée. '
                    . 'Capacité : ' . $truck->capacity . ' ' . $truck->unit
                    . '. Charge actuelle : '
                    . round($currentLoad, 3) . ' ' . $truck->unit
                    . '. Nouvelle quantité : ' . $quantity . ' '
                    . $truck->unit . '.'
                );
            }

            $site = Site::whereKey($stop->site_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Site interne : déduire le stock.
            // Site externe : pas de sortie du stock interne.
            if ($site->tracks_stock) {
                app(StockService::class)->removeStock(
                    (int) $site->id,
                    (int) $stop->product_id,
                    $quantity,
                    'mission_stop',
                    (int) $stop->id,
                    $userId,
                    'Chargement Mission #' . $mission->id
                );
            }

            // Enregistrer la quantité réellement chargée.
            // La modification et le mouvement de stock sont atomiques.
            $stop->update([
                'quantity' => $quantity,
                'loading_status' => 'loaded',
            ]);

            return $stop->fresh();
        });
    }

    public function deliverMissionStop(
        MissionStop $stop,
        float $deliveredQuantity
    ): MissionStop {
        return DB::transaction(function () use ($stop, $deliveredQuantity) {
            $stop = MissionStop::whereKey($stop->id)
                ->lockForUpdate()
                ->firstOrFail();

            $mission = Mission::whereKey($stop->mission_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureMissionActive($mission);
            $this->ensureNoReturnRecorded($mission);

            if (!in_array($stop->stop_type, ['delivery', 'unloading'], true)) {
                throw new InvalidArgumentException(
                    'Cet arrêt n’est pas un arrêt de livraison ou de déchargement.'
                );
            }

            if (!in_array($stop->delivery_status, ['pending', 'partial'], true)) {
                throw new InvalidArgumentException(
                    'Cette livraison est déjà terminée ou non applicable.'
                );
            }

            if (!is_finite($deliveredQuantity) || $deliveredQuantity <= 0) {
                throw new InvalidArgumentException(
                    'La quantité livrée doit être supérieure à zéro.'
                );
            }

            $planned = (float) $stop->quantity;
            $alreadyDelivered = (float) $stop->delivered_quantity;
            $totalDelivered = round($alreadyDelivered + $deliveredQuantity, 3);

            if ($planned <= 0 || $totalDelivered > $planned + self::EPSILON) {
                throw new InvalidArgumentException(
                    'La quantité livrée ne peut pas dépasser la quantité prévue.'
                );
            }

            $product = Product::findOrFail($stop->product_id);
            $this->ensureSameUnit($product->unit, $stop->unit);

            $loadedForProduct = (float) MissionStop::where(
                'mission_id',
                $mission->id
            )
                ->where('stop_type', 'loading')
                ->where('loading_status', 'loaded')
                ->where('product_id', $stop->product_id)
                ->where('stop_order', '<', $stop->stop_order)
                ->sum('quantity');

            $deliveredBeforeStop = (float) MissionStop::where(
                'mission_id',
                $mission->id
            )
                ->whereIn('stop_type', ['delivery', 'unloading'])
                ->where('product_id', $stop->product_id)
                ->where('stop_order', '<', $stop->stop_order)
                ->sum('delivered_quantity');

            if (
                $loadedForProduct <= 0
                || $deliveredBeforeStop + $alreadyDelivered + $deliveredQuantity
                    > $loadedForProduct + self::EPSILON
            ) {
                throw new InvalidArgumentException(
                    'La quantité livrée dépasse la quantité chargée pour ce produit.'
                );
            }

            $complete = abs($totalDelivered - $planned) < self::EPSILON;

            $stop->update([
                'delivered_quantity' => $totalDelivered,
                'delivery_status' => $complete ? 'delivered' : 'partial',
            ]);

            return $stop->fresh();
        });
    }

    /**
     * Le formulaire actuel accepte un seul site et une seule quantité
     * de retour par mission. Le retour est donc refusé si plusieurs produits
     * possèdent encore une quantité à retourner.
     */
    public function returnMissionStock(
        Mission $mission,
        int $siteId,
        float $quantity,
        ?int $userId = null
    ): Mission {
        return DB::transaction(function () use (
            $mission,
            $siteId,
            $quantity,
            $userId
        ) {
            $mission = Mission::whereKey($mission->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureMissionActive($mission);
            $this->ensureNoReturnRecorded($mission);

            if (!is_finite($quantity) || $quantity <= 0) {
                throw new InvalidArgumentException(
                    'La quantité retournée doit être supérieure à zéro.'
                );
            }

            $destination = Site::whereKey($siteId)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$destination->tracks_stock) {
                throw new InvalidArgumentException(
                    'Le site de retour doit suivre le stock.'
                );
            }

            $loadedStops = MissionStop::where('mission_id', $mission->id)
                ->where('stop_type', 'loading')
                ->where('loading_status', 'loaded')
                ->lockForUpdate()
                ->get();

            if ($loadedStops->isEmpty()) {
                throw new InvalidArgumentException(
                    'Aucun chargement confirmé pour cette mission.'
                );
            }

            if (
                MissionStop::where('mission_id', $mission->id)
                    ->where('stop_type', 'loading')
                    ->where('loading_status', 'pending')
                    ->exists()
            ) {
                throw new InvalidArgumentException(
                    'Confirmez tous les chargements avant le retour.'
                );
            }

            $loadedByProduct = $loadedStops->groupBy('product_id');
            $remainingByProduct = [];

            foreach ($loadedByProduct as $productId => $stops) {
                if (!$productId) {
                    throw new InvalidArgumentException(
                        'Un chargement ne possède pas de produit valide.'
                    );
                }

                $loaded = (float) $stops->sum('quantity');

                $delivered = (float) MissionStop::where(
                    'mission_id',
                    $mission->id
                )
                    ->whereIn('stop_type', ['delivery', 'unloading'])
                    ->where('product_id', $productId)
                    ->sum('delivered_quantity');

                $remaining = round($loaded - $delivered, 3);

                if ($remaining < -self::EPSILON) {
                    throw new InvalidArgumentException(
                        'Les quantités livrées dépassent les quantités chargées.'
                    );
                }

                if ($remaining > self::EPSILON) {
                    $remainingByProduct[(int) $productId] = $remaining;
                }
            }

            if (count($remainingByProduct) !== 1) {
                throw new InvalidArgumentException(
                    'Le formulaire actuel permet un seul produit restant à retourner. '
                    . 'Livrez le reste des autres produits ou adaptez le formulaire.'
                );
            }

            $productId = (int) array_key_first($remainingByProduct);
            $remaining = (float) $remainingByProduct[$productId];

            if (abs(round($quantity, 3) - $remaining) >= self::EPSILON) {
                throw new InvalidArgumentException(
                    'La quantité retournée doit être égale au reste : '
                    . number_format($remaining, 3, '.', '')
                );
            }

            $product = Product::findOrFail($productId);

            foreach ($loadedStops->where('product_id', $productId) as $loadedStop) {
                $sourceSite = Site::findOrFail($loadedStop->site_id);
                $this->ensureSameUnit($product->unit, $loadedStop->unit);

                if ($sourceSite->tracks_stock) {
                    $movementExists = DB::table('stock_movements')
                        ->where('reference_type', 'mission_stop')
                        ->where('reference_id', $loadedStop->id)
                        ->where('type', 'OUT')
                        ->exists();

                    if (!$movementExists) {
                        throw new InvalidArgumentException(
                            'Un chargement interne ne possède pas de sortie de stock valide.'
                        );
                    }
                }
            }

            app(StockService::class)->returnStock(
                $siteId,
                $productId,
                $remaining,
                (int) $mission->id,
                $userId
            );

            $mission->update([
                'return_site_id' => $siteId,
                'returned_quantity' => $remaining,
            ]);

            return $mission->fresh();
        });
    }

    /**
     * Vérifier chaque produit séparément avant de terminer la mission.
     */
    public function completeMission(Mission $mission): Mission
    {
        return DB::transaction(function () use ($mission) {
            $mission = Mission::whereKey($mission->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($mission->status === 'completed') {
                throw new InvalidArgumentException(
                    'Cette mission est déjà terminée.'
                );
            }

            if ($mission->status === 'cancelled') {
                throw new InvalidArgumentException(
                    'Une mission annulée ne peut pas être terminée.'
                );
            }

            $loadingStops = MissionStop::where('mission_id', $mission->id)
                ->where('stop_type', 'loading')
                ->get();

            // Préserver les missions historiques sans arrêts de chargement.
            if ($loadingStops->isNotEmpty()) {
                // Bloquer la clôture si une livraison est en attente ou partielle.
                $unfinishedDelivery = MissionStop::where(
                    'mission_id',
                    $mission->id
                )
                    ->whereIn('stop_type', ['delivery', 'unloading'])
                    ->whereIn('delivery_status', ['pending', 'partial'])
                    ->exists();

                if ($unfinishedDelivery) {
                    throw new InvalidArgumentException(
                        'Impossible de terminer : toutes les livraisons doivent être terminées.'
                    );
                }

                // Tous les chargements doivent être confirmés.
                if (
                    $loadingStops->contains(
                        fn ($stop) => $stop->loading_status !== 'loaded'
                    )
                ) {
                    throw new InvalidArgumentException(
                        'Tous les chargements doivent être confirmés avant la clôture.'
                    );
                }

                $returnMovement = DB::table('stock_movements')
                    ->where('reference_type', 'mission_return')
                    ->where('reference_id', $mission->id)
                    ->where('type', 'RETURN')
                    ->first();

                $returnedQuantity = (float) $mission->returned_quantity;

                if ($returnedQuantity > 0 && !$returnMovement) {
                    throw new InvalidArgumentException(
                        'Le retour enregistré ne possède pas de mouvement de stock.'
                    );
                }

                if ($returnMovement && $returnedQuantity <= 0) {
                    throw new InvalidArgumentException(
                        'Un mouvement de retour existe sans quantité enregistrée sur la mission.'
                    );
                }

                if ($returnedQuantity > 0) {
                    if (
                        abs(
                            round((float) $returnMovement->quantity, 3)
                            - round($returnedQuantity, 3)
                        ) >= self::EPSILON
                    ) {
                        throw new InvalidArgumentException(
                            'La quantité du mouvement de retour ne correspond pas à celle de la mission.'
                        );
                    }

                    if (!$mission->return_site_id) {
                        throw new InvalidArgumentException(
                            'Le site de retour doit être renseigné.'
                        );
                    }

                    if (
                        (int) $returnMovement->to_site_id
                        !== (int) $mission->return_site_id
                    ) {
                        throw new InvalidArgumentException(
                            'Le site du mouvement de retour ne correspond pas à la mission.'
                        );
                    }

                    $returnStock = DB::table('stocks')
                        ->where('id', $returnMovement->stock_id)
                        ->first();

                    if (!$returnStock) {
                        throw new InvalidArgumentException(
                            'Le stock associé au retour est introuvable.'
                        );
                    }

                    $returnedProductId = (int) $returnStock->product_id;
                } else {
                    $returnedProductId = null;
                }

                $loadedByProduct = $loadingStops->groupBy('product_id');

                foreach ($loadedByProduct as $productId => $stops) {
                    if (!$productId) {
                        throw new InvalidArgumentException(
                            'Un chargement ne possède pas de produit valide.'
                        );
                    }

                    $product = Product::findOrFail($productId);
                    $loadedQuantity = (float) $stops->sum('quantity');

                    foreach ($stops as $loadedStop) {
                        $sourceSite = Site::findOrFail($loadedStop->site_id);

                        $this->ensureSameUnit($product->unit, $loadedStop->unit);

                        $movementExists = DB::table('stock_movements')
                            ->where('reference_type', 'mission_stop')
                            ->where('reference_id', $loadedStop->id)
                            ->where('type', 'OUT')
                            ->exists();

                        if ($sourceSite->tracks_stock && !$movementExists) {
                            throw new InvalidArgumentException(
                                'Un chargement interne ne possède pas de sortie de stock.'
                            );
                        }

                        if (!$sourceSite->tracks_stock && $movementExists) {
                            throw new InvalidArgumentException(
                                'Un site externe possède une sortie de stock inattendue.'
                            );
                        }
                    }

                    $deliveryStops = MissionStop::where(
                        'mission_id',
                        $mission->id
                    )
                        ->whereIn('stop_type', ['delivery', 'unloading'])
                        ->where('product_id', $productId)
                        ->get();

                    $deliveredQuantity = 0.0;

                    foreach ($deliveryStops as $deliveryStop) {
                        $this->ensureSameUnit($product->unit, $deliveryStop->unit);

                        $deliveredQuantity += (float) $deliveryStop->delivered_quantity;
                    }

                    $productReturn = $returnedProductId === (int) $productId
                        ? $returnedQuantity
                        : 0.0;

                    $difference = round(
                        $loadedQuantity - $deliveredQuantity - $productReturn,
                        3
                    );

                    if (abs($difference) >= self::EPSILON) {
                        throw new InvalidArgumentException(
                            'Impossible de terminer : vérifiez les quantités du produit '
                            . $product->name
                            . '. Reste à justifier : '
                            . number_format($difference, 3, '.', '')
                        );
                    }
                }

                if (
                    $returnedProductId !== null
                    && !isset($loadedByProduct[$returnedProductId])
                ) {
                    throw new InvalidArgumentException(
                        'Le produit retourné ne correspond à aucun chargement de cette mission.'
                    );
                }
            }

            $mission->update([
                'status' => 'completed',
                'return_time' => now(),
            ]);

            return $mission->fresh();
        });
    }

    private function ensureMissionActive(Mission $mission): void
    {
        if (!in_array($mission->status, ['planned', 'in_progress'], true)) {
            throw new InvalidArgumentException(
                'Cette opération est interdite pour cette mission.'
            );
        }
    }

    private function ensureNoReturnRecorded(Mission $mission): void
    {
        $returnExists = DB::table('stock_movements')
            ->where('reference_type', 'mission_return')
            ->where('reference_id', $mission->id)
            ->where('type', 'RETURN')
            ->exists();

        if ((float) $mission->returned_quantity > 0 || $returnExists) {
            throw new InvalidArgumentException(
                'Impossible de modifier la mission après un retour de marchandise.'
            );
        }
    }

    private function ensureSameUnit(
        ?string $firstUnit,
        ?string $secondUnit
    ): void {
        if (
            trim((string) $firstUnit) === ''
            || trim((string) $secondUnit) === ''
            || trim((string) $firstUnit) !== trim((string) $secondUnit)
        ) {
            throw new InvalidArgumentException(
                'Les unités doivent être identiques et renseignées.'
            );
        }
    }
}