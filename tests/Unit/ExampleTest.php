<?php

namespace Tests\Unit;

use App\Models\Accommodation;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function reservation_nights_are_calculated_automatically()
    {
        $accommodation = Accommodation::create([
            'name' => 'Test Room',
            'type' => 'standard',
            'capacity' => 1,
            'price_per_night' => 100,
        ]);

        $customer = User::factory()->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(12);

        $reservation = Reservation::create([
            'customer_id' => $customer->id,
            'accommodation_id' => $accommodation->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
        ]);

        $this->assertEquals(7, $reservation->number_of_nights);
    }

    /** @test */
    public function reservation_total_price_is_calculated_automatically()
    {
        $accommodation = Accommodation::create([
            'name' => 'Test Room',
            'type' => 'standard',
            'capacity' => 1,
            'price_per_night' => 100,
        ]);

        $customer = User::factory()->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(15);

        $reservation = Reservation::create([
            'customer_id' => $customer->id,
            'accommodation_id' => $accommodation->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
        ]);

        $expectedPrice = 10 * $accommodation->price_per_night;
        $this->assertEquals($expectedPrice, $reservation->total_price);
    }

    /** @test */
    public function accommodation_availability_check()
    {
        $accommodation = Accommodation::create([
            'name' => 'Test Room',
            'type' => 'standard',
            'capacity' => 1,
            'price_per_night' => 100,
            'available' => true,
        ]);

        $isAvailable = $accommodation->isAvailableForDates('2024-03-10', '2024-03-15');
        $this->assertTrue($isAvailable);
    }

    /** @test */
    public function accommodation_prevents_overbooking()
    {
        $accommodation = Accommodation::create([
            'name' => 'Test Room',
            'type' => 'standard',
            'capacity' => 1,
            'price_per_night' => 100,
            'available' => true,
        ]);

        $customer = User::factory()->create();

        // Create a confirmed reservation
        $checkIn = \Carbon\Carbon::now()->addDays(5)->format('Y-m-d');
        $checkOut = \Carbon\Carbon::now()->addDays(10)->format('Y-m-d');

        Reservation::create([
            'customer_id' => $customer->id,
            'accommodation_id' => $accommodation->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'status' => 'confirmed',
        ]);

        // Check if it's available during the booking period should return false
        $isAvailable = $accommodation->isAvailableForDates(
            \Carbon\Carbon::now()->addDays(6)->format('Y-m-d'),
            \Carbon\Carbon::now()->addDays(8)->format('Y-m-d')
        );
        $this->assertFalse($isAvailable);

        // Check if it's available after the booking should return true
        $isAvailable = $accommodation->isAvailableForDates(
            \Carbon\Carbon::now()->addDays(10)->format('Y-m-d'),
            \Carbon\Carbon::now()->addDays(15)->format('Y-m-d')
        );
        $this->assertTrue($isAvailable);
    }

    /** @test */
    public function user_can_have_multiple_reservations()
    {
        $customer = User::factory()->create();
        $accommodation = Accommodation::factory()->create();

        Reservation::factory()->count(3)->create([
            'customer_id' => $customer->id,
            'accommodation_id' => $accommodation->id,
        ]);

        $this->assertCount(3, $customer->reservations);
    }

    /** @test */
    public function fake_user_detection()
    {
        $fakeUser = User::factory()->create([
            'email' => 'test@example.com',
            'is_registered' => false,
        ]);

        $realUser = User::factory()->create([
            'email' => 'john.doe@real.com',
            'is_registered' => true,
        ]);

        $this->assertTrue($fakeUser->isFake());
        $this->assertFalse($realUser->isFake());
    }

    /** @test */
    public function registered_customer_identification()
    {
        $registeredCustomer = User::factory()->create([
            'email' => 'john@real.com',
            'is_registered' => true,
            'role' => 'customer',
        ]);

        $unregisteredUser = User::factory()->create([
            'email' => 'test_user@example.com',
            'is_registered' => false,
        ]);

        $this->assertTrue($registeredCustomer->isRegisteredCustomer());
        $this->assertFalse($unregisteredUser->isRegisteredCustomer());
    }
}
