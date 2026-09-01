<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    protected $model = Deal::class;

    /**
     * Títulos comerciais usados no seed de demonstração.
     *
     * @var list<string>
     */
    private const TITLES = [
        'Proposta Comercial',
        'Renovação de Contrato',
        'Expansão de Licenças',
        'Implementação de Sistema',
        'Consultoria Estratégica',
        'Suporte Premium',
        'Projeto Piloto',
        'Contrato Anual',
        'Upgrade de Plano',
        'Migração de Dados',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'user_id'     => User::factory(),
            'title'       => fake()->randomElement(self::TITLES),
            'value'       => fake()->randomFloat(2, 500, 50000),
            'stage'       => fake()->randomElement(array_keys(Deal::STAGES)),
        ];
    }

    public function stage(string $stage): static
    {
        return $this->state(fn () => ['stage' => $stage]);
    }

    /**
     * Negócio de um cliente, herdando o vendedor responsável.
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn () => [
            'customer_id' => $customer->id,
            'user_id'     => $customer->user_id,
        ]);
    }
}
