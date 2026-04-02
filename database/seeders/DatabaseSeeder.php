<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test accommodations
        $accommodations = Accommodation::factory(10)->create();

        // Create test users
        $users = User::factory(5)->create();

        // Create test reservations associated with existing accommodations and users
        for ($i = 0; $i < 12; $i++) {
            $accommodation = $accommodations->random();
            $user = $users->random();
            $checkIn = Carbon::now()->addDays(rand(1, 30));
            $checkOut = $checkIn->clone()->addDays(rand(1, 14));

            Reservation::create([
                'customer_id' => $user->id,
                'accommodation_id' => $accommodation->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'number_of_nights' => $checkOut->diffInDays($checkIn),
                'total_price' => ($accommodation->price_per_night ?? 100) * $checkOut->diffInDays($checkIn),
                'status' => collect(['pending', 'confirmed', 'checked_in', 'completed'])->random(),
                'special_requests' => rand(0, 1) ? fake()->sentence() : null,
            ]);
        }
    }
}
