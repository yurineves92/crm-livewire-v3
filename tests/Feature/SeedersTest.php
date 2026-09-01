<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Interaction;
use App\Models\User;
use Database\Seeders\CrmDemoSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedersTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_creates_default_accounts_and_is_idempotent(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(count(UserSeeder::ACCOUNTS), User::count());
        $this->assertDatabaseHas('users', ['email' => 'admin@crm.com', 'role' => User::ROLE_ADMIN]);
        $this->assertTrue(auth()->attempt(['email' => 'admin@crm.com', 'password' => 'password']));
    }

    public function test_demo_seeder_creates_a_consistent_dataset(): void
    {
        putenv('SEED_CUSTOMERS=5');

        $this->seed(UserSeeder::class);
        $this->seed(CrmDemoSeeder::class);

        putenv('SEED_CUSTOMERS');

        $this->assertSame(5, Customer::count());
        $this->assertGreaterThan(0, Deal::count());
        $this->assertGreaterThan(0, Interaction::count());

        // Negócios e interações herdam o vendedor dono do cliente.
        $deal = Deal::with('customer')->firstOrFail();
        $this->assertSame($deal->customer->user_id, $deal->user_id);
    }
}
