<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    protected $fillable = [
        'reference',
        'truck_id',
        'driver_id',
        'departure_site_id',
        'mission_date',
        'departure_time',
        'return_time',
        'mission_type',
        'status',
        'total_distance',
        'total_quantity',
        'transport_cost',
        'fuel_cost',
        'driver_cost',
        'other_cost',
        'total_cost',
        'total_revenue',
        'profit',
        'notes',
    ];

    protected $casts = [
        'mission_date' => 'date',
        'departure_time' => 'datetime',
        'return_time' => 'datetime',
        'total_distance' => 'decimal:3',
        'total_quantity' => 'decimal:3',
        'transport_cost' => 'decimal:3',
        'fuel_cost' => 'decimal:3',
        'driver_cost' => 'decimal:3',
        'other_cost' => 'decimal:3',
        'total_cost' => 'decimal:3',
        'total_revenue' => 'decimal:3',
        'profit' => 'decimal:3',
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function departureSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'departure_site_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(MissionStop::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function fuelRecords(): HasMany
    {
        return $this->hasMany(FuelRecord::class);
    }
}