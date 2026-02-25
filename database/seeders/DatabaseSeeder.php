<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@resort.com',
            'password' => bcrypt('password'),
            'is_registered' => true,
            'role' => 'admin',
        ]);

        // Create registered customers
        $customers = User::factory()->count(5)->create([
            'is_registered' => true,
            'role' => 'customer',
        ]);

        // Create accommodations
        $accommodations = Accommodation::factory()->count(10)->create();

        // Create reservations for each customer
        foreach ($customers as $customer) {
            Reservation::factory()->count(2)->create([
                'customer_id' => $customer->id,
                'accommodation_id' => $accommodations->random()->id,
            ]);
        }
    }
}
