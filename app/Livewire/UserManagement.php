<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public ?int $editingUserId = null;
    public bool $showForm = false;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = User::ROLE_SALES;

    #[Url(except: '')]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
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
                Rule::unique('users', 'email')->ignore($this->editingUserId),
            ],
            'password' => [$this->editingUserId ? 'nullable' : 'required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(array_keys(User::ROLES))],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editUser(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editingUserId = $user->id;
        $this->name          = $user->name;
        $this->email         = $user->email;
        $this->password      = '';
        $this->role          = $user->role;
        $this->showForm      = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->password === '') {
            unset($data['password']);
        }

        if ($this->editingUserId) {
            User::findOrFail($this->editingUserId)->update($data);
        } else {
            User::create($data);
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
        $this->reset('editingUserId', 'showForm', 'name', 'email', 'password', 'role');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::query()
                ->when($this->search, fn ($query) => $query->where(
                    fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                ))
                ->latest()
                ->paginate(10),
        ]);
    }
}
