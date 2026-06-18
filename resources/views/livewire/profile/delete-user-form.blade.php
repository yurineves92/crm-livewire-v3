<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-4">

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
        Excluir Minha Conta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6 space-y-4">

            <div>
                <h3 class="text-lg font-bold text-gray-800">Confirmar exclusão de conta</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Após excluir, todos os seus dados serão permanentemente removidos.
                    Digite sua senha para confirmar.
                </p>
            </div>

            <div>
                <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Senha atual <span class="text-red-500">*</span>
                </label>
                <input wire:model="password" id="delete_password"
                    type="password" placeholder="Digite sua senha"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-red-500 focus:ring-red-500" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Sim, excluir minha conta
                </button>
            </div>

        </form>
    </x-modal>

</section>