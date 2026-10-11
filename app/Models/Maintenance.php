<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $table = 'maintenance';

    protected $fillable = [
        'truck_id',
        'maintenance_date',
        'type',
        'description',
        'current_kilometer',
        'garage',
        'cost',
        'next_maintenance_date',
        'next_maintenance_kilometer',
        'status',
        'reference',
        'notes',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'current_kilometer' => 'decimal:3',
        'cost' => 'decimal:3',
        'next_maintenance_date' => 'date',
        'next_maintenance_kilometer' => 'decimal:3',
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
}