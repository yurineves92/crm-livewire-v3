<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CRM') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex">

        {{-- Lado Esquerdo: Branding --}}
        <div class="hidden lg:flex w-1/2 bg-blue-600 flex-col items-center justify-center p-12 text-white">
            <h1 class="text-4xl font-extrabold tracking-tight mb-4">CRM Livewire</h1>
            <p class="text-blue-100 text-center text-lg max-w-sm">
                Gerencie clientes, acompanhe negócios e aumente suas vendas em tempo real.
            </p>
            <div class="mt-10 grid grid-cols-2 gap-4 w-full max-w-xs text-center text-sm font-medium">
                <div class="bg-blue-500/30 border border-blue-400/20 rounded-xl p-4">
                    <p class="text-2xl font-bold">100%</p>
                    <p class="opacity-80">Online</p>
                </div>
                <div class="bg-blue-500/30 border border-blue-400/20 rounded-xl p-4">
                    <p class="text-2xl font-bold">Real-time</p>
                    <p class="opacity-80">Livewire</p>
                </div>
            </div>
        </div>

        {{-- Lado Direito: Formulário --}}
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center p-8 bg-gray-50">
            <div class="mb-8 lg:hidden">
                <a href="/" wire:navigate>
                    <x-application-logo class="w-16 h-16 fill-current text-blue-600" />
                </a>
            </div>

            <div class="w-full max-w-sm">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Bem-vindo de volta!</h2>
                    <p class="text-gray-500 mt-1 text-sm">Acesse sua conta para continuar.</p>
                </div>

                <div class="bg-white shadow-md rounded-xl px-6 py-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

    </div>
</body>

</html>