<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Interaction;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CustomerShow extends Component
{
    public Customer $customer;

    // Negócio - Criar/Editar
    public ?int $editingDealId = null;

    #[Validate('required|min:3')]
    public string $dealTitle = '';

    #[Validate('required|numeric|min:0')]
    public string $dealValue = '';

    #[Validate('required')]
    public string $dealStage = 'prospecting';

    // Interação
    #[Validate('required|min:3')]
    public string $note = '';

    public function mount(Customer $customer): void
    {
        $user = auth()->user();

        if ($user->role === 'sales' && $customer->user_id !== $user->id) {
            abort(403);
        }

        $this->customer = $customer;
    }

    // ── Negócios ──────────────────────────────────────────

    public function addDeal(): void
    {
        $this->validateOnly('dealTitle');
        $this->validateOnly('dealValue');
        $this->validateOnly('dealStage');

        Deal::create([
            'customer_id' => $this->customer->id,
            'user_id'     => auth()->id(),
            'title'       => $this->dealTitle,
            'value'       => $this->dealValue,
            'stage'       => $this->dealStage,
        ]);

        $this->reset('dealTitle', 'dealValue', 'dealStage');
    }

    public function editDeal(int $id): void
    {
        $deal = Deal::findOrFail($id);
        $user = auth()->user();

        if ($user->role === 'sales' && $deal->user_id !== $user->id) {
            abort(403);
        }

        $this->editingDealId = $id;
        $this->dealTitle     = $deal->title;
        $this->dealValue     = $deal->value;
        $this->dealStage     = $deal->stage;
    }

    public function updateDeal(): void
    {
        $this->validateOnly('dealTitle');
        $this->validateOnly('dealValue');
        $this->validateOnly('dealStage');

        $deal = Deal::findOrFail($this->editingDealId);
        $user = auth()->user();

        if ($user->role === 'sales' && $deal->user_id !== $user->id) {
            abort(403);
        }

        $deal->update([
            'title' => $this->dealTitle,
            'value' => $this->dealValue,
            'stage' => $this->dealStage,
        ]);

        $this->reset('dealTitle', 'dealValue', 'dealStage', 'editingDealId');
    }

    public function cancelEditDeal(): void
    {
        $this->reset('dealTitle', 'dealValue', 'dealStage', 'editingDealId');
    }

    public function deleteDeal(int $id): void
    {
        $deal = Deal::findOrFail($id);
        $user = auth()->user();

        if ($user->role === 'sales' && $deal->user_id !== $user->id) {
            abort(403);
        }

        $deal->delete();
    }

    // ── Interações ────────────────────────────────────────

    public function addInteraction(): void
    {
        $this->validateOnly('note');

        Interaction::create([
            'customer_id' => $this->customer->id,
            'user_id'     => auth()->id(),
            'note'        => $this->note,
        ]);

        $this->reset('note');
    }

    public function deleteInteraction(int $id): void
    {
        $interaction = Interaction::findOrFail($id);
        $user = auth()->user();

        if ($user->role === 'sales' && $interaction->user_id !== $user->id) {
            abort(403);
        }

        $interaction->delete();
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.customer-show', [
            'deals'        => Deal::where('customer_id', $this->customer->id)
                ->when($user->role === 'sales', fn($q) => $q->where('user_id', $user->id))
                ->latest()->get(),
            'interactions' => Interaction::where('customer_id', $this->customer->id)
                ->when($user->role === 'sales', fn($q) => $q->where('user_id', $user->id))
                ->latest()->get(),
        ]);
    }
}
