<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Cabeçalho --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ $editing ? 'Editar Cliente' : 'Novo Cliente' }}
            </h2>
            <a href="{{ route('customers.index') }}" wire:navigate
                class="text-sm text-gray-500 hover:text-gray-700 transition">
                ← Voltar para lista
            </a>
        </div>

        {{-- Formulário --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-6">
                {{ $editing ? 'Editar dados do cliente' : 'Novo Cliente' }}
            </h3>

            <form wire:submit="save" class="space-y-5">

                {{-- Linha 1: Nome + Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="Nome completo"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            E-mail <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="email" type="email" placeholder="email@empresa.com"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Linha 2: Telefone + Empresa --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input wire:model="phone" type="text" placeholder="(11) 99999-9999"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
                        @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <input wire:model="company" type="text" placeholder="Nome da empresa"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
                        @error('company')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Ações --}}
                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('customers.index') }}" wire:navigate
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition flex items-center gap-2">
                        <span wire:loading.remove wire:target="save">
                            {{ $editing ? 'Salvar Alterações' : 'Criar Cliente' }}
                        </span>
                        <span wire:loading wire:target="save">Salvando...</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>