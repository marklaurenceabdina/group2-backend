<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Accommodation;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccommodationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_accommodation_has_many_reservations()
    {
        $accommodation = Accommodation::factory()->create();

        Reservation::factory()->count(5)->create([
            'accommodation_id' => $accommodation->id,
        ]);

        $this->assertCount(5, $accommodation->reservations);
    }

    public function test_accommodation_is_available_for_dates()
    {
        $accommodation = Accommodation::factory()->create(['available' => true]);

        $checkIn = \Carbon\Carbon::now()->addDays(10)->format('Y-m-d');
        $checkOut = \Carbon\Carbon::now()->addDays(15)->format('Y-m-d');

        $isAvailable = $accommodation->isAvailableForDates($checkIn, $checkOut);

        $this->assertTrue($isAvailable);
    }

    public function test_accommodation_not_available_when_disabled()
    {
        $accommodation = Accommodation::factory()->create(['available' => false]);

        $isAvailable = $accommodation->isAvailableForDates('2024-03-10', '2024-03-15');

        $this->assertFalse($isAvailable);
    }

    public function test_accommodation_unavailable_for_booked_dates()
    {
        $accommodation = Accommodation::factory()->create(['available' => true]);

        // Create a confirmed reservation
        $checkIn = \Carbon\Carbon::now()->addDays(5)->format('Y-m-d');
        $checkOut = \Carbon\Carbon::now()->addDays(10)->format('Y-m-d');

        Reservation::factory()->create([
            'accommodation_id' => $accommodation->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'status' => 'confirmed',
        ]);

        // Check if it's available during the booking period
        $isAvailable = $accommodation->isAvailableForDates(
            \Carbon\Carbon::now()->addDays(6)->format('Y-m-d'),
            \Carbon\Carbon::now()->addDays(8)->format('Y-m-d')
        );

        $this->assertFalse($isAvailable);
    }

    public function test_accommodation_available_for_non_overlapping_dates()
    {
        $accommodation = Accommodation::factory()->create(['available' => true]);

        $checkIn = \Carbon\Carbon::now()->addDays(5)->format('Y-m-d');
        $checkOut = \Carbon\Carbon::now()->addDays(10)->format('Y-m-d');

        Reservation::factory()->create([
            'accommodation_id' => $accommodation->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'status' => 'confirmed',
        ]);

        // Check availability for dates after the booking
        $isAvailable = $accommodation->isAvailableForDates(
            \Carbon\Carbon::now()->addDays(10)->format('Y-m-d'),
            \Carbon\Carbon::now()->addDays(15)->format('Y-m-d')
        );

        $this->assertTrue($isAvailable);
    }

    public function test_get_available_accommodations()
    {
        $availableAccommodation = Accommodation::factory()->create(['available' => true]);
        $unavailableAccommodation = Accommodation::factory()->create(['available' => false]);

        $available = Accommodation::availableForDates(
            \Carbon\Carbon::now()->addDays(1)->format('Y-m-d'),
            \Carbon\Carbon::now()->addDays(5)->format('Y-m-d')
        );

        $ids = $available->pluck('id')->toArray();
        $this->assertContains($availableAccommodation->id, $ids);
        $this->assertNotContains($unavailableAccommodation->id, $ids);
    }
}
