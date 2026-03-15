<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'duration',
        'bookings_today',
        'revenue',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'bookings_today' => 'integer',
        'revenue' => 'decimal:2',
    ];
}
