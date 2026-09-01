<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Interaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CrmDemoSeeder extends Seeder
{
    /**
     * Volume de clientes gerado. Ajustável por env (SEED_CUSTOMERS=200 php artisan db:seed).
     */
    private int $customerCount;

    public function __construct()
    {
        $this->customerCount = (int) env('SEED_CUSTOMERS', 40);
    }

    public function run(): void
    {
        $owners = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_SALES])->get();

        if ($owners->isEmpty()) {
            $owners = User::factory()->count(2)->sales()->create();
        }

        $now      = Carbon::now();
        $yearAgo  = $now->copy()->subYear();

        DB::transaction(function () use ($owners, $now, $yearAgo) {
            for ($i = 0; $i < $this->customerCount; $i++) {
                $createdAt = Carbon::createFromTimestamp(random_int($yearAgo->timestamp, $now->timestamp));

                $customer = Customer::factory()
                    ->ownedBy($owners->random())
                    ->create(['created_at' => $createdAt, 'updated_at' => $createdAt]);

                foreach (range(1, random_int(1, 4)) as $ignored) {
                    $dealDate = Carbon::createFromTimestamp(random_int($createdAt->timestamp, $now->timestamp));

                    Deal::factory()
                        ->forCustomer($customer)
                        ->create(['created_at' => $dealDate, 'updated_at' => $dealDate]);
                }

                foreach (range(1, random_int(1, 5)) as $ignored) {
                    $noteDate = Carbon::createFromTimestamp(random_int($createdAt->timestamp, $now->timestamp));

                    Interaction::factory()
                        ->forCustomer($customer)
                        ->create(['created_at' => $noteDate, 'updated_at' => $noteDate]);
                }
            }
        });
    }
}
