<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'stock_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'from_site_id',
        'to_site_id',
        'movement_date',
        'user_id',
        'note',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'movement_date' => 'datetime',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function fromSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'from_site_id');
    }

    public function toSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'to_site_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}