<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'unit',
        'purchase_price',
        'sale_price',
        'min_stock',
        'status',
        'description',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function missionStops(): HasMany
    {
        return $this->hasMany(MissionStop::class);
    }
}