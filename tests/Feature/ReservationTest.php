<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Accommodation;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $accommodation;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'customer', 'is_registered' => true]);
        $this->accommodation = Accommodation::factory()->create([
            'available' => true,
            'price_per_night' => 100,
        ]);
    }

    public function test_authenticated_user_can_get_own_reservations()
    {
        $reservation = Reservation::factory()->create([
            'customer_id' => $this->user->id,
            'accommodation_id' => $this->accommodation->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/reservations');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'customer_id',
                        'accommodation_id',
                        'check_in_date',
                        'check_out_date',
                        'number_of_nights',
                        'total_price',
                        'status',
                    ]
                ]
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_create_reservation_calculates_nights_and_total()
    {
        $checkIn = now()->addDays(1)->toDateString();
        $checkOut = now()->addDays(5)->toDateString(); // 4 nights

        $data = [
            'customer_id' => $this->user->id,
            'accommodation_id' => $this->accommodation->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'special_requests' => 'Early check-in if possible',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', $data);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.number_of_nights', 4)
            ->assertJsonPath('data.total_price', '400.00'); // 4 nights * 100
    }

    public function test_reservation_validation_dates()
    {
        $data = [
            'customer_id' => $this->user->id,
            'accommodation_id' => $this->accommodation->id,
            'check_in_date' => now()->addDays(5)->toDateString(),
            'check_out_date' => now()->addDays(1)->toDateString(), // Before check-in
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', $data);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_cannot_book_unavailable_accommodation()
    {
        // Create a reservation for the dates
        $existingReservation = Reservation::factory()->create([
            'accommodation_id' => $this->accommodation->id,
            'check_in_date' => now()->addDays(1)->toDateString(),
            'check_out_date' => now()->addDays(5)->toDateString(),
            'status' => 'confirmed',
        ]);

        // Try to book same accommodation for overlapping dates
        $data = [
            'customer_id' => $this->user->id,
            'accommodation_id' => $this->accommodation->id,
            'check_in_date' => now()->addDays(3)->toDateString(),
            'check_out_date' => now()->addDays(7)->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', $data);

        $response->assertStatus(409)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Accommodation is not available for the selected dates');
    }

    public function test_user_cannot_create_reservation_for_others()
    {
        $otherUser = User::factory()->create(['role' => 'customer']);

        $data = [
            'customer_id' => $otherUser->id, // Different user
            'accommodation_id' => $this->accommodation->id,
            'check_in_date' => now()->addDays(1)->toDateString(),
            'check_out_date' => now()->addDays(5)->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', $data);

        $response->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Unauthorized: You can only create reservations for yourself');
    }

    public function test_confirm_pending_reservation()
    {
        $reservation = Reservation::factory()->create([
            'customer_id' => $this->user->id,
            'accommodation_id' => $this->accommodation->id,
            'status' => 'pending',
        ]);

        $adminUser = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($adminUser)
            ->postJson("/api/reservations/{$reservation->id}/confirm");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_check_in_confirmed_reservation()
    {
        $reservation = Reservation::factory()->create([
            'customer_id' => $this->user->id,
            'accommodation_id' => $this->accommodation->id,
            'status' => 'confirmed',
        ]);

        $adminUser = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($adminUser)
            ->postJson("/api/reservations/{$reservation->id}/check-in");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'checked_in');
    }

    public function test_check_out_checked_in_reservation()
    {
        $reservation = Reservation::factory()->create([
            'customer_id' => $this->user->id,
            'accommodation_id' => $this->accommodation->id,
            'status' => 'checked_in',
        ]);

        $adminUser = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($adminUser)
            ->postJson("/api/reservations/{$reservation->id}/check-out");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');
    }

    public function test_delete_reservation()
    {
        $reservation = Reservation::factory()->create([
            'customer_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    public function test_reservation_price_calculation()
    {
        $accommodation = Accommodation::factory()->create(['price_per_night' => 150]);

        $data = [
            'customer_id' => $this->user->id,
            'accommodation_id' => $accommodation->id,
            'check_in_date' => now()->addDays(1)->toDateString(),
            'check_out_date' => now()->addDays(8)->toDateString(), // 7 nights
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.number_of_nights', 7)
            ->assertJsonPath('data.total_price', '1050.00'); // 7 * 150
    }
}
