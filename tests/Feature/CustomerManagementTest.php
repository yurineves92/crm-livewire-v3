<?php

namespace Tests\Feature;

use App\Livewire\CustomerForm;
use App\Livewire\CustomerList;
use App\Livewire\CustomerShow;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/customers')->assertRedirect('/login');
    }

    public function test_customer_list_is_displayed(): void
    {
        $user     = User::factory()->manager()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($user)
            ->get('/customers')
            ->assertOk()
            ->assertSee($customer->name);
    }

    public function test_search_filters_customers_by_name_email_or_company(): void
    {
        $user = User::factory()->manager()->create();

        Customer::factory()->create(['name' => 'Maria Aparecida', 'company' => 'Alpha Sistemas']);
        Customer::factory()->create(['name' => 'Carlos Souza', 'company' => 'Beta Consultoria']);

        Livewire::actingAs($user)
            ->test(CustomerList::class)
            ->set('search', 'Alpha')
            ->assertSee('Maria Aparecida')
            ->assertDontSee('Carlos Souza');
    }

    public function test_sales_user_only_sees_own_customers(): void
    {
        $sales = User::factory()->sales()->create();
        $other = User::factory()->sales()->create();

        Customer::factory()->ownedBy($sales)->create(['name' => 'Cliente do João']);
        Customer::factory()->ownedBy($other)->create(['name' => 'Cliente da Ana']);

        Livewire::actingAs($sales)
            ->test(CustomerList::class)
            ->assertSee('Cliente do João')
            ->assertDontSee('Cliente da Ana');
    }

    public function test_customer_can_be_created(): void
    {
        $user = User::factory()->sales()->create();

        Livewire::actingAs($user)
            ->test(CustomerForm::class)
            ->set('name', 'Novo Cliente')
            ->set('email', 'novo@empresa.com')
            ->set('phone', '(11) 90000-0000')
            ->set('company', 'Empresa Nova')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'email'   => 'novo@empresa.com',
            'user_id' => $user->id,
        ]);
    }

    public function test_customer_creation_validates_required_fields(): void
    {
        $user     = User::factory()->sales()->create();
        $existing = Customer::factory()->create();

        Livewire::actingAs($user)
            ->test(CustomerForm::class)
            ->set('name', 'ab')
            ->set('email', $existing->email)
            ->call('save')
            ->assertHasErrors(['name' => 'min', 'email' => 'unique']);

        $this->assertSame(1, Customer::count());
    }

    public function test_customer_can_be_updated(): void
    {
        $user     = User::factory()->manager()->create();
        $customer = Customer::factory()->create(['name' => 'Nome Antigo']);

        Livewire::actingAs($user)
            ->test(CustomerForm::class, ['customer' => $customer])
            ->assertSet('editing', true)
            ->set('name', 'Nome Atualizado')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Nome Atualizado', $customer->fresh()->name);
    }

    public function test_customer_can_be_deleted(): void
    {
        $user     = User::factory()->manager()->create();
        $customer = Customer::factory()->create();

        Livewire::actingAs($user)
            ->test(CustomerList::class)
            ->call('delete', $customer->id);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_sales_user_cannot_open_customer_of_another_seller(): void
    {
        $sales    = User::factory()->sales()->create();
        $customer = Customer::factory()->create();

        Livewire::actingAs($sales)
            ->test(CustomerShow::class, ['customer' => $customer])
            ->assertStatus(403);
    }

    public function test_sales_user_cannot_delete_customer_of_another_seller(): void
    {
        $sales    = User::factory()->sales()->create();
        $customer = Customer::factory()->create();

        Livewire::actingAs($sales)
            ->test(CustomerList::class)
            ->call('delete', $customer->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }
}
