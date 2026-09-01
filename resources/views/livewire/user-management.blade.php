<div class="page">
    <div class="page-container">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="page-title">Gestão de Usuários</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $users->total() }} usuário(s) cadastrado(s).</p>
            </div>
            @unless ($showForm)
                <button wire:click="openCreate" class="btn-primary">+ Novo Usuário</button>
            @endunless
        </div>

        @if ($showForm)
            <div class="card p-6">
                <h2 class="card-title mb-4">{{ $editingUserId ? 'Editar Usuário' : 'Novo Usuário' }}</h2>

                <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Nome *</label>
                        <input wire:model="name" type="text" placeholder="Nome completo" class="input">
                        @error('name') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">E-mail *</label>
                        <input wire:model="email" type="email" placeholder="email@empresa.com" class="input">
                        @error('email') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">
                            Senha {{ $editingUserId ? '(deixe em branco para manter)' : '*' }}
                        </label>
                        <input wire:model="password" type="password" placeholder="Mínimo 8 caracteres" class="input">
                        @error('password') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">Perfil *</label>
                        <select wire:model="role" class="select">
                            @foreach (\App\Models\User::ROLES as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2 flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="cancelForm" class="btn-ghost">Cancelar</button>
                        <button type="submit" class="btn-primary">
                            <span wire:loading.remove wire:target="save">
                                {{ $editingUserId ? 'Atualizar' : 'Criar Usuário' }}
                            </span>
                            <span wire:loading wire:target="save">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="w-full sm:w-96">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nome ou e-mail..."
                class="input py-2">
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="th">Nome</th>
                            <th class="th">E-mail</th>
                            <th class="th">Perfil</th>
                            <th class="th text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}" @class([
                                'hover:bg-slate-50 transition',
                                'bg-brand-50/60' => $user->id === auth()->id(),
                            ])>
                                <td class="td font-semibold text-slate-800">
                                    {{ $user->name }}
                                    @if ($user->id === auth()->id())
                                        <span class="ml-1 text-xs font-normal text-brand-600">(você)</span>
                                    @endif
                                </td>
                                <td class="td">{{ $user->email }}</td>
                                <td class="td">
                                    <span @class([
                                        'badge-rose' => $user->role === \App\Models\User::ROLE_ADMIN,
                                        'badge-violet' => $user->role === \App\Models\User::ROLE_MANAGER,
                                        'badge-green' => $user->role === \App\Models\User::ROLE_SALES,
                                    ])>{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</span>
                                </td>
                                <td class="td text-right space-x-3 whitespace-nowrap">
                                    <button wire:click="editUser({{ $user->id }})" class="link-action">Editar</button>
                                    @if ($user->id !== auth()->id())
                                        <button wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Excluir o usuário {{ $user->name }}?"
                                            class="link-danger">Excluir</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="table-empty">Nenhum usuário encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
