<?php

namespace App\Livewire;

use App\Livewire\Concerns\ScopesToUser;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Interaction;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CustomerShow extends Component
{
    use ScopesToUser;

    public Customer $customer;

    // Negócio — criar/editar
    public ?int $editingDealId = null;
    public string $dealTitle = '';
    public string $dealValue = '';
    public string $dealStage = Deal::STAGE_PROSPECTING;

    // Interação
    public string $note = '';

    public function mount(Customer $customer): void
    {
        $this->authorizeOwnership($customer);

        $this->customer = $customer;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'dealTitle' => ['required', 'string', 'min:3', 'max:255'],
            'dealValue' => ['required', 'numeric', 'min:0'],
            'dealStage' => ['required', Rule::in(array_keys(Deal::STAGES))],
            'note'      => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    /**
     * Subconjunto das regras, já que a tela tem dois formulários independentes.
     *
     * @return array<string, mixed>
     */
    protected function rulesFor(string ...$keys): array
    {
        return array_intersect_key($this->rules(), array_flip($keys));
    }

    // ── Negócios ──────────────────────────────────────────

    public function addDeal(): void
    {
        $this->validate($this->rulesFor('dealTitle', 'dealValue', 'dealStage'));

        Deal::create([
            'customer_id' => $this->customer->id,
            'user_id'     => auth()->id(),
            'title'       => $this->dealTitle,
            'value'       => $this->dealValue,
            'stage'       => $this->dealStage,
        ]);

        $this->resetDealForm();
    }

    public function editDeal(int $id): void
    {
        $deal = $this->findDeal($id);

        $this->editingDealId = $deal->id;
        $this->dealTitle     = $deal->title;
        $this->dealValue     = (string) $deal->value;
        $this->dealStage     = $deal->stage;
    }

    public function updateDeal(): void
    {
        $this->validate($this->rulesFor('dealTitle', 'dealValue', 'dealStage'));

        $this->findDeal($this->editingDealId)->update([
            'title' => $this->dealTitle,
            'value' => $this->dealValue,
            'stage' => $this->dealStage,
        ]);

        $this->resetDealForm();
    }

    public function cancelEditDeal(): void
    {
        $this->resetDealForm();
    }

    public function deleteDeal(int $id): void
    {
        $this->findDeal($id)->delete();

        if ($this->editingDealId === $id) {
            $this->resetDealForm();
        }
    }

    private function findDeal(?int $id): Deal
    {
        $deal = Deal::where('customer_id', $this->customer->id)->findOrFail($id);

        $this->authorizeOwnership($deal);

        return $deal;
    }

    private function resetDealForm(): void
    {
        $this->reset('dealTitle', 'dealValue', 'dealStage', 'editingDealId');
        $this->resetValidation();
    }

    // ── Interações ────────────────────────────────────────

    public function addInteraction(): void
    {
        $this->validate($this->rulesFor('note'));

        Interaction::create([
            'customer_id' => $this->customer->id,
            'user_id'     => auth()->id(),
            'note'        => $this->note,
        ]);

        $this->reset('note');
    }

    public function deleteInteraction(int $id): void
    {
        $interaction = Interaction::where('customer_id', $this->customer->id)->findOrFail($id);

        $this->authorizeOwnership($interaction);

        $interaction->delete();
    }

    public function render()
    {
        return view('livewire.customer-show', [
            'deals' => $this->scoped(Deal::class)
                ->where('customer_id', $this->customer->id)
                ->latest()
                ->get(),
            'interactions' => $this->scoped(Interaction::class)
                ->where('customer_id', $this->customer->id)
                ->latest()
                ->get(),
        ]);
    }
}
