<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Interaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Popula o banco com os usuários padrão e uma base de demonstração.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CrmDemoSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->table(
            ['Tabela', 'Registros'],
            [
                ['users', User::count()],
                ['customers', Customer::count()],
                ['deals', Deal::count()],
                ['interactions', Interaction::count()],
            ],
        );
        $this->command?->info('Login: admin@crm.com / manager@crm.com / joao@crm.com / ana@crm.com — senha: password');
    }
}
