<?php

namespace Tests\Feature;

use App\Livewire\CustomerShow;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Interaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DealAndInteractionTest extends TestCase
{
    use RefreshDatabase;

    private function customerFor(User $user): Customer
    {
        return Customer::factory()->ownedBy($user)->create();
    }

    public function test_deal_can_be_added_to_customer(): void
    {
        $user     = User::factory()->sales()->create();
        $customer = $this->customerFor($user);

        Livewire::actingAs($user)
            ->test(CustomerShow::class, ['customer' => $customer])
            ->set('dealTitle', 'Contrato Anual')
            ->set('dealValue', '1500.50')
            ->set('dealStage', Deal::STAGE_PROPOSAL)
            ->call('addDeal')
            ->assertHasNoErrors()
            ->assertSet('dealTitle', '');

        $this->assertDatabaseHas('deals', [
            'customer_id' => $customer->id,
            'user_id'     => $user->id,
            'title'       => 'Contrato Anual',
            'stage'       => Deal::STAGE_PROPOSAL,
        ]);
    }

    public function test_deal_validation_rejects_invalid_stage_and_value(): void
    {
        $user     = User::factory()->sales()->create();
        $customer = $this->customerFor($user);

        Livewire::actingAs($user)
            ->test(CustomerShow::class, ['customer' => $customer])
            ->set('dealTitle', 'ok')
            ->set('dealValue', 'abc')
            ->set('dealStage', 'inexistente')
            ->call('addDeal')
            ->assertHasErrors(['dealTitle', 'dealValue', 'dealStage']);

        $this->assertSame(0, Deal::count());
    }

    public function test_deal_can_be_edited_and_deleted(): void
    {
        $user     = User::factory()->manager()->create();
        $customer = $this->customerFor($user);
        $deal     = Deal::factory()->forCustomer($customer)->create(['title' => 'Original']);

        $component = Livewire::actingAs($user)
            ->test(CustomerShow::class, ['customer' => $customer])
            ->call('editDeal', $deal->id)
            ->assertSet('dealTitle', 'Original')
            ->set('dealTitle', 'Renegociado')
            ->set('dealStage', Deal::STAGE_WON)
            ->call('updateDeal')
            ->assertHasNoErrors()
            ->assertSet('editingDealId', null);

        $this->assertDatabaseHas('deals', [
            'id'    => $deal->id,
            'title' => 'Renegociado',
            'stage' => Deal::STAGE_WON,
        ]);

        $component->call('deleteDeal', $deal->id);

        $this->assertDatabaseMissing('deals', ['id' => $deal->id]);
    }

    public function test_sales_user_cannot_touch_deal_of_another_seller(): void
    {
        $sales    = User::factory()->sales()->create();
        $customer = Customer::factory()->ownedBy($sales)->create();
        $deal     = Deal::factory()->create([
            'customer_id' => $customer->id,
            'user_id'     => User::factory()->sales()->create()->id,
        ]);

        Livewire::actingAs($sales)
            ->test(CustomerShow::class, ['customer' => $customer])
            ->call('deleteDeal', $deal->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('deals', ['id' => $deal->id]);
    }

    public function test_interaction_can_be_added_and_deleted(): void
    {
        $user     = User::factory()->sales()->create();
        $customer = $this->customerFor($user);

        $component = Livewire::actingAs($user)
            ->test(CustomerShow::class, ['customer' => $customer])
            ->set('note', 'Ligação realizada com sucesso.')
            ->call('addInteraction')
            ->assertHasNoErrors()
            ->assertSet('note', '');

        $interaction = Interaction::firstOrFail();
        $this->assertSame($customer->id, $interaction->customer_id);

        $component->call('deleteInteraction', $interaction->id);

        $this->assertSame(0, Interaction::count());
    }

    public function test_interaction_requires_a_note(): void
    {
        $user     = User::factory()->sales()->create();
        $customer = $this->customerFor($user);

        Livewire::actingAs($user)
            ->test(CustomerShow::class, ['customer' => $customer])
            ->set('note', '')
            ->call('addInteraction')
            ->assertHasErrors(['note' => 'required']);
    }

    public function test_deleting_customer_cascades_to_deals_and_interactions(): void
    {
        $user     = User::factory()->manager()->create();
        $customer = $this->customerFor($user);

        Deal::factory()->forCustomer($customer)->create();
        Interaction::factory()->forCustomer($customer)->create();

        $customer->delete();

        $this->assertSame(0, Deal::count());
        $this->assertSame(0, Interaction::count());
    }
}
