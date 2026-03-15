<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'nationality',
        'total_stays',
        'total_spent',
        'last_visit',
    ];

    protected $casts = [
        'total_stays' => 'integer',
        'total_spent' => 'decimal:2',
        'last_visit' => 'date',
    ];
}
