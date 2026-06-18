<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Cabeçalho --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Gestão de Usuários</h2>
            @if(!$showForm)
            <button wire:click="openCreate"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                + Novo Usuário
            </button>
            @endif
        </div>

        {{-- Formulário Criar/Editar --}}
        @if($showForm)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                {{ $editingUserId ? '✏️ Editar Usuário' : 'Novo Usuário' }}
            </h3>
            <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome *</label>
                    <input wire:model="name" type="text" placeholder="Nome completo"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail *</label>
                    <input wire:model="email" type="email" placeholder="email@empresa.com"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Senha {{ $editingUserId ? '(deixe em branco para não alterar)' : '*' }}
                    </label>
                    <input wire:model="password" type="password" placeholder="Mínimo 8 caracteres"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Perfil *</label>
                    <select wire:model="role"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="sales">Sales</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2 flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="cancelForm"
                        class="text-sm font-semibold text-gray-500 hover:text-gray-700 px-4 py-2 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded-lg transition">
                        <span wire:loading.remove>{{ $editingUserId ? 'Atualizar' : 'Criar Usuário' }}</span>
                        <span wire:loading>Salvando...</span>
                    </button>
                </div>

            </form>
        </div>
        @endif

        {{-- Busca --}}
        <div>
            <input wire:model.live="search" type="text" placeholder="Buscar por nome ou e-mail..."
                class="w-full sm:w-96 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
        </div>

        {{-- Tabela --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">E-mail</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Perfil</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr @class(['hover:bg-gray-50', 'bg-blue-50'=> $user->id === auth()->id()])>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                            <span class="ml-1 text-xs text-blue-500 font-normal">(você)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span @class([ 'px-2 py-1 text-xs font-bold rounded-full' , 'bg-red-100 text-red-700'=> $user->role === 'admin',
                                'bg-purple-100 text-purple-700' => $user->role === 'manager',
                                'bg-green-100 text-green-700' => $user->role === 'sales',
                                ])>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="editUser({{ $user->id }})"
                                class="text-yellow-600 hover:underline text-sm font-medium">
                                Editar
                            </button>
                            @if($user->id !== auth()->id())
                            <button wire:click="deleteUser({{ $user->id }})"
                                wire:confirm="Excluir o usuário {{ $user->name }}?"
                                class="text-red-500 hover:underline text-sm font-medium">
                                Excluir
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhum usuário encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginação --}}
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
            @endif
        </div>

    </div>
</div>