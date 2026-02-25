<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_accommodations()
    {
        Accommodation::create([
            'name' => 'Standard Room',
            'type' => 'standard',
            'capacity' => 1,
            'price_per_night' => 50,
        ]);

        Accommodation::create([
            'name' => 'Deluxe Suite',
            'type' => 'deluxe',
            'capacity' => 2,
            'price_per_night' => 100,
        ]);

        $response = $this->getJson('/api/accommodations');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function can_create_accommodation()
    {
        $response = $this->postJson('/api/accommodations', [
            'name' => 'Luxury Villa',
            'description' => 'An amazing villa',
            'type' => 'villa',
            'capacity' => 6,
            'price_per_night' => 500,
            'available' => true,
            'amenities' => ['pool', 'garden', 'wifi'],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Luxury Villa');
        $response->assertJsonPath('data.price_per_night', '500.00');

        $this->assertDatabaseHas('accommodations', [
            'name' => 'Luxury Villa',
            'type' => 'villa',
        ]);
    }

    /** @test */
    public function can_update_accommodation()
    {
        $accommodation = Accommodation::create([
            'name' => 'Standard Room',
            'type' => 'standard',
            'capacity' => 1,
            'price_per_night' => 50,
        ]);

        $response = $this->putJson("/api/accommodations/{$accommodation->id}", [
            'name' => 'Premium Standard Room',
            'price_per_night' => 75,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Premium Standard Room');
        $response->assertJsonPath('data.price_per_night', '75.00');
    }

    /** @test */
    public function can_delete_accommodation()
    {
        $accommodation = Accommodation::create([
            'name' => 'Standard Room',
            'type' => 'standard',
            'capacity' => 1,
            'price_per_night' => 50,
        ]);

        $response = $this->deleteJson("/api/accommodations/{$accommodation->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('accommodations', ['id' => $accommodation->id]);
    }

    /** @test */
    public function can_get_available_accommodations_for_dates()
    {
        $accommodation = Accommodation::create([
            'name' => 'Deluxe Suite',
            'type' => 'deluxe',
            'capacity' => 2,
            'price_per_night' => 100,
            'available' => true,
        ]);

        $checkInDate = Carbon::now()->addDays(5)->format('Y-m-d');
        $checkOutDate = Carbon::now()->addDays(10)->format('Y-m-d');

        $response = $this->postJson('/api/accommodations/available', [
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function unavailable_accommodation_is_not_returned()
    {
        Accommodation::create([
            'name' => 'Deluxe Suite',
            'type' => 'deluxe',
            'capacity' => 2,
            'price_per_night' => 100,
            'available' => false,
        ]);

        $checkInDate = Carbon::now()->addDays(5)->format('Y-m-d');
        $checkOutDate = Carbon::now()->addDays(10)->format('Y-m-d');

        $response = $this->postJson('/api/accommodations/available', [
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }
}
