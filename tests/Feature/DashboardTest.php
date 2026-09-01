<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_the_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_is_displayed_for_authenticated_users(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Dashboard');
    }

    public function test_kpis_reflect_deals_in_the_selected_period(): void
    {
        $user     = User::factory()->manager()->create();
        $customer = Customer::factory()->ownedBy($user)->create();

        Deal::factory()->forCustomer($customer)->stage(Deal::STAGE_WON)->create(['value' => 1000]);
        Deal::factory()->forCustomer($customer)->stage(Deal::STAGE_PROPOSAL)->create(['value' => 400]);
        Deal::factory()->forCustomer($customer)->stage(Deal::STAGE_LOST)->create(['value' => 900]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertViewHas('totalDeals', 3)
            ->assertViewHas('closedWon', 1000.0)
            ->assertViewHas('pipeline', 400.0);
    }

    public function test_period_filter_ignores_older_records(): void
    {
        $user     = User::factory()->manager()->create();
        $customer = Customer::factory()->ownedBy($user)->create();

        Deal::factory()->forCustomer($customer)->create(['created_at' => now()->subDays(60)]);
        Deal::factory()->forCustomer($customer)->create(['created_at' => now()->subDay()]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('period', '1year')
            ->assertViewHas('totalDeals', 2)
            ->set('period', '30days')
            ->assertViewHas('totalDeals', 1)
            ->set('period', '7days')
            ->assertViewHas('totalDeals', 1);
    }

    public function test_sales_user_only_sees_own_numbers(): void
    {
        $sales = User::factory()->sales()->create();
        $other = User::factory()->sales()->create();

        Deal::factory()->forCustomer(Customer::factory()->ownedBy($sales)->create())->create();
        Deal::factory()->forCustomer(Customer::factory()->ownedBy($other)->create())->create();

        Livewire::actingAs($sales)
            ->test(Dashboard::class)
            ->assertViewHas('totalDeals', 1)
            ->assertViewHas('totalCustomers', 1);
    }

    public function test_csv_exports_are_downloadable(): void
    {
        $user     = User::factory()->manager()->create();
        $customer = Customer::factory()->ownedBy($user)->create(['name' => 'Cliente Exportado']);
        Deal::factory()->forCustomer($customer)->create(['title' => 'Negócio Exportado']);

        $component = Livewire::actingAs($user)->test(Dashboard::class);

        $customersCsv = $component->call('exportCustomers')->effects['download'] ?? null;
        $this->assertNotNull($customersCsv);
        $this->assertStringContainsString('Cliente Exportado', base64_decode($customersCsv['content']));

        $dealsCsv = $component->call('exportDeals')->effects['download'] ?? null;
        $this->assertNotNull($dealsCsv);
        $this->assertStringContainsString('Negócio Exportado', base64_decode($dealsCsv['content']));
    }
}
