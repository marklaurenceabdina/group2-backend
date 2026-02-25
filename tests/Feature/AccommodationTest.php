<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Accommodation;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccommodationTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_accommodations()
    {
        // Create sample accommodations
        Accommodation::factory()->count(3)->create();

        $response = $this->getJson('/api/accommodations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'type',
                        'capacity',
                        'price_per_night',
                        'available',
                    ]
                ],
                'message'
            ])
            ->assertJsonPath('success', true);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_create_accommodation()
    {
        $data = [
            'name' => 'Deluxe Suite',
            'description' => 'Luxury accommodation',
            'type' => 'suite',
            'capacity' => 2,
            'price_per_night' => 150.50,
            'available' => true,
        ];

        $response = $this->postJson('/api/accommodations', $data);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'data', 'message']);

        $this->assertDatabaseHas('accommodations', [
            'name' => 'Deluxe Suite',
            'type' => 'suite',
        ]);
    }

    public function test_create_accommodation_validation()
    {
        $data = [
            'name' => '', // Required
            'price_per_night' => -10, // Should be >= 0
        ];

        $response = $this->postJson('/api/accommodations', $data);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors', 'message']);
    }

    public function test_get_accommodation_by_id()
    {
        $accommodation = Accommodation::factory()->create();

        $response = $this->getJson("/api/accommodations/{$accommodation->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $accommodation->id)
            ->assertJsonPath('data.name', $accommodation->name);
    }

    public function test_update_accommodation()
    {
        $accommodation = Accommodation::factory()->create();
        $updatedData = ['name' => 'Updated Name', 'price_per_night' => 200];

        $response = $this->putJson("/api/accommodations/{$accommodation->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('accommodations', [
            'id' => $accommodation->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_delete_accommodation()
    {
        $accommodation = Accommodation::factory()->create();

        $response = $this->deleteJson("/api/accommodations/{$accommodation->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('accommodations', ['id' => $accommodation->id]);
    }

    public function test_get_available_accommodations_for_dates()
    {
        $accommodation1 = Accommodation::factory()->create(['available' => true]);
        $accommodation2 = Accommodation::factory()->create(['available' => false]);

        $checkIn = \Carbon\Carbon::now()->addDays(1)->format('Y-m-d');
        $checkOut = \Carbon\Carbon::now()->addDays(5)->format('Y-m-d');

        $response = $this->postJson('/api/accommodations/available', [
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Should include only available accommodations
        $data = $response->json('data');
        $ids = array_column($data, 'id');
        $this->assertContains($accommodation1->id, $ids);
    }
}
