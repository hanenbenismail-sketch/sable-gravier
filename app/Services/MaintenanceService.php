<?php

namespace App\Services;

use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MaintenanceService
{
    public function createMaintenance(array $data): Maintenance
    {
        return DB::transaction(function () use ($data) {

            $truckId = $data['truck_id'] ?? null;
            $cost = (float) ($data['cost'] ?? 0);

            if (!$truckId) {
                throw new InvalidArgumentException(
                    'Le camion est obligatoire.'
                );
            }

            if (empty($data['maintenance_date'])) {
                $data['maintenance_date'] = now()->toDateString();
            }

            if (empty($data['type'])) {
                throw new InvalidArgumentException(
                    'Le type de maintenance est obligatoire.'
                );
            }

            if (empty($data['description'])) {
                throw new InvalidArgumentException(
                    'La description de la maintenance est obligatoire.'
                );
            }

            if ($cost < 0) {
                throw new InvalidArgumentException(
                    'Le coût de la maintenance ne peut pas être négatif.'
                );
            }

            return Maintenance::create([
                'truck_id' => $truckId,
                'maintenance_date' => $data['maintenance_date'],
                'type' => $data['type'],
                'description' => $data['description'],
                'current_kilometer' => $data['current_kilometer'] ?? null,
                'garage' => $data['garage'] ?? null,
                'cost' => $cost,
                'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
                'next_maintenance_kilometer' => $data['next_maintenance_kilometer'] ?? null,
                'status' => $data['status'] ?? 'completed',
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
