<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'accommodation_id',
        'check_in_date',
        'check_out_date',
        'number_of_nights',
        'total_price',
        'status',
        'special_requests',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_price' => 'decimal:2',
        'number_of_nights' => 'integer',
    ];

    /**
     * Get the customer for this reservation
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the accommodation for this reservation
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Calculate number of nights
     */
    public function calculateNights(): int
    {
        if (empty($this->check_in_date) || empty($this->check_out_date)) {
            return 0;
        }

        $checkIn = Carbon::parse($this->check_in_date);
        $checkOut = Carbon::parse($this->check_out_date);

        return (int) abs($checkOut->diffInDays($checkIn));
    }

    /**
     * Calculate total price
     */
    public function calculateTotalPrice(): float
    {
        $nights = $this->calculateNights();
        if (!$this->accommodation || !isset($this->accommodation->price_per_night)) {
            return 0.0;
        }

        return $nights * $this->accommodation->price_per_night;
    }

    /**
     * Boot method to automatically calculate values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (!empty($reservation->check_in_date) && !empty($reservation->check_out_date)) {
                $reservation->number_of_nights = $reservation->calculateNights();
            }

            $reservation->total_price = $reservation->calculateTotalPrice();
            if (empty($reservation->status)) {
                $reservation->status = 'pending';
            }
        });

        static::updating(function ($reservation) {
            if (!empty($reservation->check_in_date) && !empty($reservation->check_out_date)) {
                $reservation->number_of_nights = $reservation->calculateNights();
            }
            $reservation->total_price = $reservation->calculateTotalPrice();
        });
    }

    /**
     * Check in the reservation
     */
    public function checkIn(): bool
    {
        if ($this->status === 'confirmed') {
            $this->update(['status' => 'checked_in']);
            return true;
        }
        return false;
    }

    /**
     * Check out the reservation
     */
    public function checkOut(): bool
    {
        if ($this->status === 'checked_in') {
            $this->update(['status' => 'completed']);
            return true;
        }
        return false;
    }
}
