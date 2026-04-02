<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertServicesData extends Migration
{
    public function up()
    {
        DB::table('services')->insert([
            [
                'name' => 'Spa Massage',
                'category' => 'Wellness',
                'price' => 1500,
                'duration' => '60 min',
                'bookings_today' => 3,
                'revenue' => 4500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beach Yoga',
                'category' => 'Wellness',
                'price' => 800,
                'duration' => '90 min',
                'bookings_today' => 5,
                'revenue' => 4000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Water Sports',
                'category' => 'Activities',
                'price' => 2000,
                'duration' => '2 hours',
                'bookings_today' => 2,
                'revenue' => 4000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fine Dining',
                'category' => 'Dining',
                'price' => 3500,
                'duration' => '2 hours',
                'bookings_today' => 4,
                'revenue' => 14000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Snorkeling Tour',
                'category' => 'Activities',
                'price' => 2500,
                'duration' => '3 hours',
                'bookings_today' => 3,
                'revenue' => 7500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Private Chef',
                'category' => 'Dining',
                'price' => 5000,
                'duration' => '3 hours',
                'bookings_today' => 1,
                'revenue' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        DB::table('services')->truncate();
    }
}
