<?php

namespace Database\Factories;

use App\Models\Accommodation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccommodationFactory extends Factory
{
    protected $model = Accommodation::class;

    public function definition(): array
    {
        $types = ['standard', 'deluxe', 'suite', 'villa'];

        return [
            'name' => $this->faker->words(3, true) . ' Room',
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement($types),
            'capacity' => $this->faker->numberBetween(1, 8),
            'price_per_night' => $this->faker->numberBetween(50, 500),
            'available' => $this->faker->boolean(90),
            'image_url' => $this->faker->imageUrl(640, 480),
            'amenities' => json_encode($this->faker->words(3)),
        ];
    }
}
