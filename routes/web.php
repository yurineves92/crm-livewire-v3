<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\CustomerList;
use App\Livewire\CustomerForm;
use App\Livewire\CustomerShow;
use App\Livewire\UserManagement;

// Redireciona a home para o dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Rotas do CRM
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/customers', CustomerList::class)->name('customers.index');
    Route::get('/customers/create', CustomerForm::class)->name('customers.create');
    Route::get('/customers/{customer}', CustomerShow::class)->name('customers.show');
    Route::get('/customers/{customer}/edit', CustomerForm::class)->name('customers.edit');
    Route::get('/users', UserManagement::class)->name('users.index');

    // Rota de Perfil (configurada pelo Breeze)
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__ . '/auth.php';
