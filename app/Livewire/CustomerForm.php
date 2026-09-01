<?php

namespace App\Livewire;

use App\Livewire\Concerns\ScopesToUser;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CustomerForm extends Component
{
    use ScopesToUser;

    public ?Customer $customer = null;
    public bool $editing = false;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';

    public function mount(?Customer $customer = null): void
    {
        if (! $customer || ! $customer->exists) {
            return;
        }

        $this->authorizeOwnership($customer);

        $this->customer = $customer;
        $this->editing  = true;
        $this->name     = $customer->name;
        $this->email    = $customer->email;
        $this->phone    = $customer->phone ?? '';
        $this->company  = $customer->company ?? '';
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'min:3', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($this->customer?->id),
            ],
            'phone'   => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editing) {
            $this->customer->update($data);
        } else {
            Customer::create($data + ['user_id' => auth()->id()]);
        }

        session()->flash('status', $this->editing ? 'Cliente atualizado.' : 'Cliente criado.');

        $this->redirect(route('customers.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.customer-form');
    }
}
