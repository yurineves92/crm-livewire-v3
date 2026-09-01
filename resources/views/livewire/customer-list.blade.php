<div class="page">
    <div class="page-container">

        {{-- Cabeçalho --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="page-title">Clientes</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $customers->total() }} cliente(s) na sua visão.</p>
            </div>
            <a href="{{ route('customers.create') }}" wire:navigate class="btn-primary">+ Novo Cliente</a>
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- Busca --}}
        <div class="relative w-full sm:w-96">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Buscar por nome, e-mail ou empresa..." class="input py-2">
            <span wire:loading wire:target="search"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">buscando...</span>
        </div>

        {{-- Tabela --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="th">Nome</th>
                            <th class="th">E-mail</th>
                            <th class="th">Telefone</th>
                            <th class="th">Empresa</th>
                            <th class="th text-center">Negócios</th>
                            <th class="th text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($customers as $customer)
                            <tr wire:key="customer-{{ $customer->id }}" class="hover:bg-slate-50 transition">
                                <td class="td font-semibold text-slate-800">
                                    <a href="{{ route('customers.show', $customer) }}" wire:navigate
                                        class="hover:text-brand-600">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="td">{{ $customer->email }}</td>
                                <td class="td">{{ $customer->phone ?? '—' }}</td>
                                <td class="td">{{ $customer->company ?? '—' }}</td>
                                <td class="td text-center">
                                    <span class="badge-slate">{{ $customer->deals_count }}</span>
                                </td>
                                <td class="td text-right space-x-3 whitespace-nowrap">
                                    <a href="{{ route('customers.show', $customer) }}" wire:navigate
                                        class="link-action">Ver</a>
                                    <a href="{{ route('customers.edit', $customer) }}" wire:navigate
                                        class="link-action">Editar</a>
                                    <button wire:click="delete({{ $customer->id }})"
                                        wire:confirm="Tem certeza que deseja excluir este cliente?"
                                        class="link-danger">Excluir</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-empty">Nenhum cliente encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
