<div class="page">
    <div class="page-container max-w-3xl">

        <div class="flex items-center justify-between">
            <h1 class="page-title">{{ $editing ? 'Editar Cliente' : 'Novo Cliente' }}</h1>
            <a href="{{ route('customers.index') }}" wire:navigate class="text-sm text-slate-500 hover:text-slate-800">
                ← Voltar para a lista
            </a>
        </div>

        <div class="card p-6">
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="label">Nome <span class="text-rose-500">*</span></label>
                        <input wire:model="name" id="name" type="text" placeholder="Nome completo" class="input">
                        @error('name') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="label">E-mail <span class="text-rose-500">*</span></label>
                        <input wire:model="email" id="email" type="email" placeholder="email@empresa.com"
                            class="input">
                        @error('email') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="label">Telefone</label>
                        <input wire:model="phone" id="phone" type="text" placeholder="(11) 99999-9999" class="input">
                        @error('phone') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="company" class="label">Empresa</label>
                        <input wire:model="company" id="company" type="text" placeholder="Nome da empresa"
                            class="input">
                        @error('company') <p class="error-text">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('customers.index') }}" wire:navigate class="btn-ghost">Cancelar</a>
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="save">
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
