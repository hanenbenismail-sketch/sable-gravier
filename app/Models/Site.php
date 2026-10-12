<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'name',
        'type',
        'address',
        'gouvernorat',
        'delegation',
        'latitude',
        'longitude',
        'status',
        'description',
        'tracks_stock',
    ];

    protected function casts(): array
    {
        return [
            'tracks_stock' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function outgoingMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'departure_site_id');
    }

    public function missionStops(): HasMany
    {
        return $this->hasMany(MissionStop::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
