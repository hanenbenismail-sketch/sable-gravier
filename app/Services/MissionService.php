<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\MissionStop;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MissionService
{
    public function calculateCosts(Mission $mission): Mission
    {
        return DB::transaction(function () use ($mission) {
            $mission->loadMissing([
                'truck',
                'driver',
                'fuelRecords',
            ]);

            // Carburant : utiliser les FuelRecord s'ils existent,
            // sinon conserver le montant saisi manuellement.
            $fuelRecords = $mission->fuelRecords;

            $fuelCost = $fuelRecords->isNotEmpty()
                ? (float) $fuelRecords->sum('total_amount')
                : (float) $mission->fuel_cost;

            // Calcul du coût du chauffeur selon son type de salaire.
            $driver = $mission->driver;
            $totalDistance = (float) $mission->total_distance;
            $driverCost = 0.0;

            if ($driver) {
                $driverCost = match ($driver->salary_type) {
                    'per_trip' => (float) $driver->salary_per_trip,
                    'per_km' => $totalDistance * (float) $driver->salary_per_km,
                    'daily' => (float) $driver->salary_per_day,
                    'monthly' => (float) $driver->base_salary / 26,
                    default => 0.0,
                };

                $driverCost += (float) $driver->bonus;
            }

            // Conserver les autres coûts saisis sur la mission.
            $otherCost = (float) $mission->other_cost;
            $transportCost = (float) $mission->transport_cost;

            // Calcul du coût total et du bénéfice.
            $totalCost = $fuelCost
                + $driverCost
                + $otherCost
                + $transportCost;

            $totalRevenue = (float) $mission->total_revenue;
            $profit = $totalRevenue - $totalCost;

            $mission->update([
                'fuel_cost' => $fuelCost,
                'driver_cost' => $driverCost,
                'other_cost' => $otherCost,
                'transport_cost' => $transportCost,
                'total_cost' => $totalCost,
                'profit' => $profit,
            ]);

            return $mission->fresh();
        });
    }

    public function loadMissionStop(
        MissionStop $stop,
        ?int $userId = null
    ): MissionStop {
        return DB::transaction(function () use ($stop, $userId) {

            // Verrouiller le stop.
            $stop = MissionStop::whereKey($stop->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Verrouiller la mission.
            $mission = Mission::whereKey($stop->mission_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($mission->status, ['planned', 'in_progress'], true)) {
                throw new InvalidArgumentException(
                    'Le chargement est interdit pour cette mission.'
                );
            }

            if ($stop->stop_type !== 'loading') {
                throw new InvalidArgumentException(
                    'Ce stop n\'est pas un stop de chargement.'
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

            $quantity = (float) $stop->quantity;

            if ($quantity <= 0) {
                throw new InvalidArgumentException(
                    'La quantité à charger doit être supérieure à zéro.'
                );
            }

            // Empêcher une seconde sortie de stock pour ce stop.
            $movementExists = DB::table('stock_movements')
                ->where('reference_type', 'mission_stop')
                ->where('reference_id', $stop->id)
                ->where('type', 'OUT')
                ->exists();

            if ($movementExists) {
                throw new InvalidArgumentException(
                    'Un mouvement de sortie existe déjà pour ce stop.'
                );
            }

            $truck = $mission->truck;

            if (!$truck) {
                throw new InvalidArgumentException(
                    'Le camion de la mission est obligatoire.'
                );
            }

            $capacity = (float) $truck->capacity;

            if ($capacity <= 0) {
                throw new InvalidArgumentException(
                    'La capacité du camion doit être supérieure à zéro.'
                );
            }

            // Quantité chargée sur cette mission.
            $loadedQuantity = (float) MissionStop::where(
                'mission_id',
                $mission->id
            )
                ->where('stop_type', 'loading')
                ->where('loading_status', 'loaded')
                ->where('id', '!=', $stop->id)
                ->sum('quantity');

            // Quantité déjà livrée ou déchargée.
            $deliveredQuantity = (float) MissionStop::where(
                'mission_id',
                $mission->id
            )
                ->whereIn('stop_type', ['delivery', 'unloading'])
                ->whereIn('delivery_status', ['delivered', 'partial'])
                ->sum('quantity');

            $currentLoad = max(
                $loadedQuantity - $deliveredQuantity,
                0
            );

            $newTotalLoad = $currentLoad + $quantity;

            if ($newTotalLoad > $capacity) {
                throw new InvalidArgumentException(
                    'Capacité du camion dépassée. '
                    . 'Capacité : ' . $capacity . ' ' . $truck->unit
                    . '. Charge actuelle : ' . $currentLoad . ' ' . $truck->unit
                    . '. Nouvelle quantité : ' . $quantity . ' ' . $truck->unit
                    . '.'
                );
            }

            // Retrait du stock et création du mouvement OUT.
            app(StockService::class)->removeStock(
                (int) $stop->site_id,
                (int) $stop->product_id,
                $quantity,
                'mission_stop',
                (int) $stop->id,
                $userId,
                'Chargement Mission #' . $mission->id
            );

            $stop->update([
                'loading_status' => 'loaded',
            ]);

            return $stop->fresh();
        });
    }

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

            $mission->update([
                'status' => 'completed',
                'return_time' => now(),
            ]);

            return $mission->fresh();
        });
    }
}
