<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <form wire:submit="updatePassword" class="space-y-5">

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">
                Senha Atual <span class="text-red-500">*</span>
            </label>
            <input wire:model="current_password" id="update_password_current_password"
                type="password" autocomplete="current-password"
                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-1 text-xs text-red-500" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">
                Nova Senha <span class="text-red-500">*</span>
            </label>
            <input wire:model="password" id="update_password_password"
                type="password" autocomplete="new-password"
                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirmar Nova Senha <span class="text-red-500">*</span>
            </label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation"
                type="password" autocomplete="new-password"
                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-500" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                Atualizar Senha
            </button>
            <x-action-message on="password-updated">
                <span class="text-sm text-green-600 font-medium">✓ Senha atualizada!</span>
            </x-action-message>
        </div>

    </form>
</section>