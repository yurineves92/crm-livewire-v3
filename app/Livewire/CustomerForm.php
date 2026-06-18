<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CustomerForm extends Component
{
    public ?Customer $customer = null;
    public bool $editing = false;

    #[Validate('required|min:3')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('nullable')]
    public string $phone = '';

    #[Validate('nullable')]
    public string $company = '';

    public function mount(?Customer $customer = null): void
    {
        if ($customer && $customer->exists) {
            // Verifica permissão: sales só edita os seus
            if (auth()->user()->role === 'sales' && $customer->user_id !== auth()->id()) {
                abort(403);
            }

            $this->customer = $customer;
            $this->editing  = true;
            $this->name     = $customer->name;
            $this->email    = $customer->email;
            $this->phone    = $customer->phone ?? '';
            $this->company  = $customer->company ?? '';
        }
    }

    public function save(): void
    {
        $rules = $this->editing
            ? ['email' => 'required|email|unique:customers,email,' . $this->customer->id]
            : ['email' => 'required|email|unique:customers,email'];

        $this->validate($rules);

        if ($this->editing) {
            $this->customer->update([
                'name'    => $this->name,
                'email'   => $this->email,
                'phone'   => $this->phone,
                'company' => $this->company,
            ]);
        } else {
            Customer::create([
                'user_id' => auth()->id(),
                'name'    => $this->name,
                'email'   => $this->email,
                'phone'   => $this->phone,
                'company' => $this->company,
            ]);
        }

        $this->redirect(route('customers.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.customer-form');
    }
}
