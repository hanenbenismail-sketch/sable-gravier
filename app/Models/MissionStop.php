<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionStop extends Model
{
    protected $fillable = [
        'mission_id',
        'stop_order',
        'stop_type',
        'site_id',
        'client_id',
        'product_id',
        'quantity',
        'delivered_quantity',
        'unit',
        'distance_from_previous',
        'loading_status',
        'delivery_status',
        'arrival_time',
        'departure_time',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'delivered_quantity' => 'decimal:3',
        'distance_from_previous' => 'decimal:3',
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
