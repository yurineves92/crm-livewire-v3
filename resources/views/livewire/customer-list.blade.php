<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Cabeçalho --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Clientes</h2>
            <a href="{{ route('customers.create') }}" wire:navigate
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                + Novo Cliente
            </a>
        </div>

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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">E-mail</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Telefone</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                            <a href="{{ route('customers.show', $customer) }}" wire:navigate class="hover:text-blue-600">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->company ?? '—' }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('customers.show', $customer) }}" wire:navigate
                                class="text-blue-600 hover:underline text-sm font-medium">Ver</a>
                            <a href="{{ route('customers.edit', $customer) }}" wire:navigate
                                class="text-yellow-600 hover:underline text-sm font-medium">Editar</a>
                            <button wire:click="delete({{ $customer->id }})"
                                wire:confirm="Tem certeza que deseja excluir este cliente?"
                                class="text-red-500 hover:underline text-sm font-medium">
                                Excluir
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhum cliente encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginação --}}
            @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
            @endif
        </div>

    </div>
</div>