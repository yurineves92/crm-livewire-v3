<div class="page">
    <div class="page-container max-w-5xl">

        {{-- Cabeçalho --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('customers.index') }}" wire:navigate
                    class="text-sm text-slate-400 hover:text-slate-700">← Voltar</a>
                <h1 class="page-title">{{ $customer->name }}</h1>
            </div>
            <a href="{{ route('customers.edit', $customer) }}" wire:navigate class="btn-secondary">Editar cliente</a>
        </div>

        {{-- Dados do cliente --}}
        <div class="card p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach ([
                'E-mail' => $customer->email,
                'Telefone' => $customer->phone ?? '—',
                'Empresa' => $customer->company ?? '—',
            ] as $label => $value)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800 break-words">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        {{-- Negócios --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">Negócios</h2>
                <span class="badge-slate">{{ $deals->count() }}</span>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <form wire:submit="{{ $editingDealId ? 'updateDeal' : 'addDeal' }}"
                    class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-start">

                    @if ($editingDealId)
                        <p class="sm:col-span-4 text-xs font-semibold uppercase text-amber-600">Editando negócio</p>
                    @endif

                    <div class="sm:col-span-2">
                        <label class="label text-xs">Título *</label>
                        <input wire:model="dealTitle" type="text" placeholder="Ex: Proposta Comercial"
                            class="input py-2">
                        @error('dealTitle') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label text-xs">Valor (R$) *</label>
                        <input wire:model="dealValue" type="number" step="0.01" placeholder="0,00" class="input py-2">
                        @error('dealValue') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label text-xs">Estágio *</label>
                        <select wire:model="dealStage" class="select py-2">
                            @foreach (\App\Models\Deal::STAGES as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('dealStage') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-4 flex justify-end gap-2">
                        @if ($editingDealId)
                            <button type="button" wire:click="cancelEditDeal" class="btn-ghost">Cancelar</button>
                        @endif
                        <button type="submit" class="btn-primary">
                            {{ $editingDealId ? 'Salvar Alterações' : '+ Adicionar Negócio' }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="th">Título</th>
                            <th class="th">Valor</th>
                            <th class="th">Estágio</th>
                            <th class="th text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($deals as $deal)
                            <tr wire:key="deal-{{ $deal->id }}" @class([
                                'hover:bg-slate-50 transition',
                                'bg-amber-50' => $editingDealId === $deal->id,
                            ])>
                                <td class="td font-semibold text-slate-800">{{ $deal->title }}</td>
                                <td class="td">R$ {{ number_format($deal->value, 2, ',', '.') }}</td>
                                <td class="td">
                                    <span @class([
                                        'badge-blue' => $deal->stage === \App\Models\Deal::STAGE_PROSPECTING,
                                        'badge-amber' => $deal->stage === \App\Models\Deal::STAGE_PROPOSAL,
                                        'badge-orange' => $deal->stage === \App\Models\Deal::STAGE_NEGOTIATION,
                                        'badge-green' => $deal->stage === \App\Models\Deal::STAGE_WON,
                                        'badge-rose' => $deal->stage === \App\Models\Deal::STAGE_LOST,
                                    ])>{{ $deal->stage_label }}</span>
                                </td>
                                <td class="td text-right space-x-3 whitespace-nowrap">
                                    <button wire:click="editDeal({{ $deal->id }})" class="link-action">Editar</button>
                                    <button wire:click="deleteDeal({{ $deal->id }})" wire:confirm="Excluir este negócio?"
                                        class="link-danger">Excluir</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="table-empty">Nenhum negócio cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Interações --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">Interações</h2>
                <span class="badge-slate">{{ $interactions->count() }}</span>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <form wire:submit="addInteraction" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                    <div class="flex-1">
                        <label class="label text-xs">Anotação *</label>
                        <input wire:model="note" type="text"
                            placeholder="Ex: Ligação realizada, cliente interessado..." class="input py-2">
                        @error('note') <p class="error-text">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn-primary whitespace-nowrap">+ Adicionar</button>
                </form>
            </div>

            <ul class="divide-y divide-slate-100">
                @forelse ($interactions as $interaction)
                    <li wire:key="interaction-{{ $interaction->id }}"
                        class="px-6 py-4 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-800">{{ $interaction->note }}</p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $interaction->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <button wire:click="deleteInteraction({{ $interaction->id }})"
                            wire:confirm="Excluir esta interação?" class="link-danger shrink-0">Excluir</button>
                    </li>
                @empty
                    <li class="table-empty">Nenhuma interação registrada.</li>
                @endforelse
            </ul>
        </div>

    </div>
</div>
