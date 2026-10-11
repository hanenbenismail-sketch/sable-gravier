<?php

namespace App\Services;

use App\Models\FuelRecord;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FuelService
{
    public function createFuelRecord(array $data): FuelRecord
    {
        return DB::transaction(function () use ($data) {

            $truckId = $data['truck_id'] ?? null;
            $liters = (float) ($data['liters'] ?? 0);
            $pricePerLiter = (float) ($data['price_per_liter'] ?? 0);

            if (!$truckId) {
                throw new InvalidArgumentException(
                    'Le camion est obligatoire.'
                );
            }

            if ($liters <= 0) {
                throw new InvalidArgumentException(
                    'La quantité de carburant doit être supérieure à zéro.'
                );
            }

            if ($pricePerLiter <= 0) {
                throw new InvalidArgumentException(
                    'Le prix par litre doit être supérieur à zéro.'
                );
            }

            $totalAmount = $liters * $pricePerLiter;

            return FuelRecord::create([
                'truck_id' => $truckId,
                'driver_id' => $data['driver_id'] ?? null,
                'mission_id' => $data['mission_id'] ?? null,
                'fuel_date' => $data['fuel_date']
                    ?? now()->toDateString(),
                'kilometer' => $data['kilometer'] ?? null,
                'liters' => $liters,
                'price_per_liter' => $pricePerLiter,
                'total_amount' => $totalAmount,
                'fuel_station' => $data['fuel_station'] ?? null,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}