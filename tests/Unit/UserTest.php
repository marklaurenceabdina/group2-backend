<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Accommodation;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_customer_can_make_reservations()
    {
        $user = User::factory()->create([
            'email' => 'john@real.com',
            'is_registered' => true,
            'role' => 'customer',
        ]);

        $this->assertTrue($user->isRegisteredCustomer());
        $this->assertFalse($user->isFake());
    }

    public function test_fake_user_is_identified()
    {
        $fakeUser = User::factory()->create([
            'email' => 'test@example.com',
            'is_registered' => false,
        ]);

        $this->assertTrue($fakeUser->isFake());
        $this->assertFalse($fakeUser->isRegisteredCustomer());
    }

    public function test_fake_user_with_demo_email()
    {
        $demoUser = User::factory()->create([
            'email' => 'demo_user@example.com',
        ]);

        $this->assertTrue($demoUser->isFake());
    }

    public function test_fake_user_with_example_email()
    {
        $exampleUser = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $this->assertTrue($exampleUser->isFake());
    }

    public function test_user_has_many_reservations()
    {
        $user = User::factory()->create();
        $accommodation = Accommodation::factory()->create();

        Reservation::factory()->count(3)->create([
            'customer_id' => $user->id,
            'accommodation_id' => $accommodation->id,
        ]);

        $this->assertCount(3, $user->reservations);
    }

    public function test_user_has_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('customer'));
        $this->assertTrue($customer->hasRole('customer'));
        $this->assertFalse($customer->hasRole('admin'));
    }
}
