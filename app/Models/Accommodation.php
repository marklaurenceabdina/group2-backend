<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodation extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'type',
        'capacity',
        'price_per_night',
        'available',
        'image_url',
        'amenities',
    ];

    protected $casts = [
        'amenities' => 'array',
        'available' => 'boolean',
        'price_per_night' => 'decimal:2',
    ];

    /**
     * Get all reservations for this accommodation
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Check if accommodation is available for given dates
     */
    public function isAvailableForDates($checkInDate, $checkOutDate): bool
    {
        if (!$this->available) {
            return false;
        }

        // Compare dates using date-only comparisons to avoid time-of-day mismatches
        return !$this->reservations()
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereDate('check_in_date', '<', $checkOutDate)
                    ->whereDate('check_out_date', '>', $checkInDate)
                    ->whereIn('status', ['confirmed', 'checked_in']);
            })
            ->exists();
    }

    /**
     * Get available accommodations for given dates
     */
    public static function availableForDates($checkInDate, $checkOutDate)
    {
        return self::where('available', true)
            ->get()
            ->filter(function ($accommodation) use ($checkInDate, $checkOutDate) {
                return $accommodation->isAvailableForDates($checkInDate, $checkOutDate);
            });
    }
}
