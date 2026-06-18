<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public string $search = '';

    // Reseta para página 1 ao buscar
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $user = auth()->user();
        $customer = Customer::findOrFail($id);

        if ($user->role === 'sales' && $customer->user_id !== $user->id) {
            abort(403);
        }

        $customer->delete();
    }

    public function render()
    {
        $user = auth()->user();

        $customers = Customer::query()
            ->when($user->role === 'sales', fn($q) => $q->where('user_id', $user->id))
            ->where(
                fn($q) => $q
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
            )
            ->latest()
            ->paginate(15);

        return view('livewire.customer-list', compact('customers'));
    }
}
