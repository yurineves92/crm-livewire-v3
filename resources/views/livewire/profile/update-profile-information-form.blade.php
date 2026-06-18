<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <form wire:submit="updateProfileInformation" class="space-y-5">

        {{-- Nome --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Nome <span class="text-red-500">*</span>
            </label>
            <input wire:model="name" id="name" name="name" type="text"
                required autofocus autocomplete="name"
                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error class="mt-1 text-xs text-red-500" :messages="$errors->get('name')" />
        </div>

        {{-- E-mail --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                E-mail <span class="text-red-500">*</span>
            </label>
            <input wire:model="email" id="email" name="email" type="email"
                required autocomplete="username"
                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error class="mt-1 text-xs text-red-500" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
            <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                {{ __('Seu e-mail não está verificado.') }}
                <button wire:click.prevent="sendVerification"
                    class="underline font-semibold hover:text-yellow-900 ml-1">
                    Reenviar verificação
                </button>
                @if (session('status') === 'verification-link-sent')
                <p class="mt-1 font-medium text-green-600">
                    Link de verificação enviado!
                </p>
                @endif
            </div>
            @endif
        </div>

        {{-- Ações --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                Salvar Alterações
            </button>

            <x-action-message on="profile-updated">
                <span class="text-sm text-green-600 font-medium">✓ Salvo com sucesso!</span>
            </x-action-message>
        </div>

    </form>
</section>