<?php

namespace App\Livewire;

use App\Livewire\Concerns\ScopesToUser;
use App\Models\Customer;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use ScopesToUser, WithPagination;

    #[Url(except: '')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $customer = Customer::findOrFail($id);

        $this->authorizeOwnership($customer);

        $customer->delete();

        $this->dispatch('notify', message: 'Cliente excluído com sucesso.');
    }

    public function render()
    {
        return view('livewire.customer-list', [
            'customers' => $this->scoped(Customer::class)
                ->search($this->search)
                ->withCount('deals')
                ->latest()
                ->paginate(15),
        ]);
    }
}
