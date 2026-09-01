<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Interaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interaction>
 */
class InteractionFactory extends Factory
{
    protected $model = Interaction::class;

    /**
     * @var list<string>
     */
    private const NOTES = [
        'Ligação realizada, cliente demonstrou interesse.',
        'E-mail de follow-up enviado.',
        'Reunião presencial agendada.',
        'Proposta enviada por e-mail.',
        'Cliente solicitou ajuste no valor.',
        'Demo do produto realizada com sucesso.',
        'Contrato revisado e enviado para assinatura.',
        'Negociação em andamento, desconto solicitado.',
        'Check-in mensal realizado.',
        'Visita técnica agendada.',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'user_id'     => User::factory(),
            'note'        => fake()->randomElement(self::NOTES),
        ];
    }

    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn () => [
            'customer_id' => $customer->id,
            'user_id'     => $customer->user_id,
        ]);
    }
}
