<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Usuários fixos usados para login em ambiente de desenvolvimento.
     * A senha de todos é "password".
     */
    public const ACCOUNTS = [
        ['name' => 'Admin Master',  'email' => 'admin@crm.com',   'role' => User::ROLE_ADMIN],
        ['name' => 'Gerente Silva', 'email' => 'manager@crm.com', 'role' => User::ROLE_MANAGER],
        ['name' => 'Vendedor João', 'email' => 'joao@crm.com',    'role' => User::ROLE_SALES],
        ['name' => 'Vendedora Ana', 'email' => 'ana@crm.com',     'role' => User::ROLE_SALES],
    ];

    public function run(): void
    {
        foreach (self::ACCOUNTS as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name'              => $account['name'],
                    'role'              => $account['role'],
                    'password'          => 'password',
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
