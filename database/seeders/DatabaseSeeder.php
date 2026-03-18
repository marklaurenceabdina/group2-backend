<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test accommodations
        Accommodation::factory(10)->create();

        // Create test users
        User::factory(5)->create();

        // Create test reservations
        Reservation::factory(12)->create();
    }
}
