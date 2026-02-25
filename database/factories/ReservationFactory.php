<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Accommodation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $checkIn = Carbon::now()->addDays($this->faker->numberBetween(1, 30));
        $checkOut = $checkIn->clone()->addDays($this->faker->numberBetween(1, 14));

        return [
            'customer_id' => User::factory(),
            'accommodation_id' => Accommodation::factory(),
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'number_of_nights' => $checkOut->diffInDays($checkIn),
            'total_price' => 0, // Will be calculated in model
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'checked_in', 'completed']),
            'special_requests' => $this->faker->optional()->sentence(),
        ];
    }
}
