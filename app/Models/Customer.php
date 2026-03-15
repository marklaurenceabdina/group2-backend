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
        'address',
        'total_stays',
        'total_spent',
        'last_visit',
    ];

    protected $casts = [
        'total_stays' => 'integer',
        'total_spent' => 'decimal:2',
        'last_visit' => 'date',
    ];

    // Provide a virtual `address` attribute that maps to the existing `nationality` DB column.
    public function getAddressAttribute()
    {
        return $this->attributes['nationality'] ?? null;
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['nationality'] = $value;
    }
}
