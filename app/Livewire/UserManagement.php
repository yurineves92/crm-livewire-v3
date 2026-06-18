<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public ?int $editingUserId = null;
    public bool $showForm = false;

    #[Validate('required|min:3')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('nullable|min:8')]
    public string $password = '';

    #[Validate('required|in:admin,manager,sales')]
    public string $role = 'sales';

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editUser(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editingUserId = $id;
        $this->name          = $user->name;
        $this->email         = $user->email;
        $this->password      = '';
        $this->role          = $user->role;
        $this->showForm      = true;
    }

    public function save(): void
    {
        $emailRule = $this->editingUserId
            ? 'required|email|unique:users,email,' . $this->editingUserId
            : 'required|email|unique:users,email';

        $passwordRule = $this->editingUserId
            ? 'nullable|min:8'
            : 'required|min:8';

        $this->validate([
            'name'     => 'required|min:3',
            'email'    => $emailRule,
            'password' => $passwordRule,
            'role'     => 'required|in:admin,manager,sales',
        ]);

        if ($this->editingUserId) {
            $data = [
                'name'  => $this->name,
                'email' => $this->email,
                'role'  => $this->role,
            ];

            if ($this->password) {
                $data['password'] = bcrypt($this->password);
            }

            User::findOrFail($this->editingUserId)->update($data);
        } else {
            User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => bcrypt($this->password),
                'role'     => $this->role,
            ]);
        }

        $this->resetForm();
    }

    public function deleteUser(int $id): void
    {
        if ($id === auth()->id()) {
            return;
        }

        User::findOrFail($id)->delete();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingUserId = null;
        $this->showForm      = false;
        $this->name          = '';
        $this->email         = '';
        $this->password      = '';
        $this->role          = 'sales';
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.user-management', compact('users'));
    }
}
