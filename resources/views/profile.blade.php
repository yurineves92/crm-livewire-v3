<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div>
                <h2 class="text-2xl font-bold text-gray-800">Meu Perfil</h2>
                <p class="text-sm text-gray-500 mt-1">Gerencie suas informações pessoais e segurança da conta.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-1">Informações Pessoais</h3>
                <p class="text-sm text-gray-400 mb-6">Atualize seu nome e endereço de e-mail.</p>
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-1">Alterar Senha</h3>
                <p class="text-sm text-gray-400 mb-6">Use uma senha longa e aleatória para manter sua conta segura.</p>
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                <h3 class="text-base font-semibold text-red-600 mb-1">Zona de Perigo</h3>
                <p class="text-sm text-gray-400 mb-6">Após excluir sua conta, todos os dados serão permanentemente removidos.</p>
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>