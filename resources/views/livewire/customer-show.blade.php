<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Cabeçalho --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-2xl font-bold text-gray-800">{{ $customer->name }}</h2>
        </div>

        {{-- Dados do Cliente --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-400 font-medium uppercase text-xs">E-mail</p>
                <p class="text-gray-800 font-semibold mt-1">{{ $customer->email }}</p>
            </div>
            <div>
                <p class="text-gray-400 font-medium uppercase text-xs">Telefone</p>
                <p class="text-gray-800 font-semibold mt-1">{{ $customer->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400 font-medium uppercase text-xs">Empresa</p>
                <p class="text-gray-800 font-semibold mt-1">{{ $customer->company ?? '—' }}</p>
            </div>
        </div>

        {{-- Negócios --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Negócios</h3>
            </div>

            {{-- Formulário Criar / Editar Negócio --}}
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <form wire:submit="{{ $editingDealId ? 'updateDeal' : 'addDeal' }}"
                    class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">

                    @if($editingDealId)
                    <div class="sm:col-span-4">
                        <span class="text-xs font-semibold text-yellow-600 uppercase">✏️ Editando negócio</span>
                    </div>
                    @endif

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Título *</label>
                        <input wire:model="dealTitle" type="text" placeholder="Ex: Proposta Comercial"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                        @error('dealTitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Valor (R$) *</label>
                        <input wire:model="dealValue" type="number" step="0.01" placeholder="0,00"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                        @error('dealValue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Estágio *</label>
                        <select wire:model="dealStage"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                            <option value="prospecting">Prospecção</option>
                            <option value="proposal">Proposta</option>
                            <option value="negotiation">Negociação</option>
                            <option value="closed_won">Ganho ✅</option>
                            <option value="closed_lost">Perdido ❌</option>
                        </select>
                    </div>
                    <div class="sm:col-span-4 flex justify-end gap-2">
                        @if($editingDealId)
                        <button type="button" wire:click="cancelEditDeal"
                            class="text-sm font-semibold text-gray-500 hover:text-gray-700 px-4 py-2 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            Salvar Alterações
                        </button>
                        @else
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            + Adicionar Negócio
                        </button>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Lista de Negócios --}}
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Estágio</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($deals as $deal)
                    <tr @class(['hover:bg-gray-50', 'bg-yellow-50 border-l-4 border-yellow-400'=> $editingDealId === $deal->id])>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $deal->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">R$ {{ number_format($deal->value, 2, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span @class([ 'px-2 py-1 text-xs font-bold rounded-full' , 'bg-blue-100 text-blue-700'=> $deal->stage === 'prospecting',
                                'bg-yellow-100 text-yellow-700' => $deal->stage === 'proposal',
                                'bg-orange-100 text-orange-700' => $deal->stage === 'negotiation',
                                'bg-green-100 text-green-700' => $deal->stage === 'closed_won',
                                'bg-red-100 text-red-700' => $deal->stage === 'closed_lost',
                                ])>
                                {{ match($deal->stage) {
                                    'prospecting' => 'Prospecção',
                                    'proposal'    => 'Proposta',
                                    'negotiation' => 'Negociação',
                                    'closed_won'  => 'Ganho',
                                    'closed_lost' => 'Perdido',
                                    default       => $deal->stage
                                } }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="editDeal({{ $deal->id }})"
                                class="text-yellow-600 hover:underline text-sm font-medium">
                                Editar
                            </button>
                            <button wire:click="deleteDeal({{ $deal->id }})"
                                wire:confirm="Excluir este negócio?"
                                class="text-red-500 hover:underline text-sm font-medium">
                                Excluir
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">
                            Nenhum negócio cadastrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Interações --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Interações</h3>
            </div>

            {{-- Formulário de Nova Interação --}}
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <form wire:submit="addInteraction" class="flex gap-3 items-end">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Anotação *</label>
                        <input wire:model="note" type="text" placeholder="Ex: Ligação realizada, cliente interessado..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                        @error('note') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                        + Adicionar
                    </button>
                </form>
            </div>

            {{-- Lista de Interações --}}
            <ul class="divide-y divide-gray-100">
                @forelse($interactions as $interaction)
                <li class="px-6 py-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-800">{{ $interaction->note }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $interaction->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <button wire:click="deleteInteraction({{ $interaction->id }})"
                        wire:confirm="Excluir esta interação?"
                        class="text-red-400 hover:text-red-600 text-xs font-medium shrink-0">
                        Excluir
                    </button>
                </li>
                @empty
                <li class="px-6 py-8 text-center text-sm text-gray-400">
                    Nenhuma interação registrada.
                </li>
                @endforelse
            </ul>
        </div>

    </div>
    </di