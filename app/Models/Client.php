<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'code',
        'name',
        'company_name',
        'phone',
        'phone_secondary',
        'email',
        'address',
        'gouvernorat',
        'delegation',
        'tax_number',
        'client_type',
        'status',
        'credit_limit',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:3',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function missionStops(): HasMany
    {
        return $this->hasMany(MissionStop::class);
    }
}