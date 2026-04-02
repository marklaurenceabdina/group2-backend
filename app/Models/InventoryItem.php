<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'min_stock',
        'unit',
        'last_updated',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_stock' => 'integer',
        'last_updated' => 'datetime',
    ];
}
