<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'license_number',
        'license_category',
        'salary_type',
        'base_salary',
        'salary_per_day',
        'salary_per_trip',
        'salary_per_km',
        'bonus',
        'status',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:3',
        'salary_per_day' => 'decimal:3',
        'salary_per_trip' => 'decimal:3',
        'salary_per_km' => 'decimal:3',
        'bonus' => 'decimal:3',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
