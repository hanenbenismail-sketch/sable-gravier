<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Truck extends Model
{
    protected $fillable = [
        'registration_number',
        'brand',
        'model',
        'capacity',
        'unit',
        'current_kilometer',
        'fuel_consumption',
        'status',
        'insurance_expiry',
        'technical_visit_expiry',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'decimal:3',
        'current_kilometer' => 'integer',
        'fuel_consumption' => 'decimal:3',
        'insurance_expiry' => 'date',
        'technical_visit_expiry' => 'date',
    ];

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    public function fuelRecords(): HasMany
    {
        return $this->hasMany(FuelRecord::class);
    }

    public function maintenance(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
