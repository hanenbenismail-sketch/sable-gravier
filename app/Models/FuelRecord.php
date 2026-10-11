<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelRecord extends Model
{
    protected $fillable = [
        'truck_id',
        'driver_id',
        'mission_id',
        'fuel_date',
        'kilometer',
        'liters',
        'price_per_liter',
        'total_amount',
        'fuel_station',
        'reference',
        'notes',
    ];

    protected $casts = [
        'fuel_date' => 'date',
        'kilometer' => 'decimal:3',
        'liters' => 'decimal:3',
        'price_per_liter' => 'decimal:3',
        'total_amount' => 'decimal:3',
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
