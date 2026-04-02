<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Spa Massage',
                'category' => 'Wellness',
                'price' => 1500,
                'duration' => '60 min',
                'bookings_today' => 3,
                'revenue' => 4500,
            ],
            [
                'name' => 'Beach Yoga',
                'category' => 'Wellness',
                'price' => 800,
                'duration' => '90 min',
                'bookings_today' => 5,
                'revenue' => 4000,
            ],
            [
                'name' => 'Water Sports',
                'category' => 'Activities',
                'price' => 2000,
                'duration' => '2 hours',
                'bookings_today' => 2,
                'revenue' => 4000,
            ],
            [
                'name' => 'Fine Dining',
                'category' => 'Dining',
                'price' => 3500,
                'duration' => '2 hours',
                'bookings_today' => 4,
                'revenue' => 14000,
            ],
            [
                'name' => 'Snorkeling Tour',
                'category' => 'Activities',
                'price' => 2500,
                'duration' => '3 hours',
                'bookings_today' => 3,
                'revenue' => 7500,
            ],
            [
                'name' => 'Private Chef',
                'category' => 'Dining',
                'price' => 5000,
                'duration' => '3 hours',
                'bookings_today' => 1,
                'revenue' => 5000,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
