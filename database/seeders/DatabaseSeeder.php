<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuários fixos ─────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin Master',
            'email'    => 'admin@crm.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $manager = User::create([
            'name'     => 'Gerente Silva',
            'email'    => 'manager@crm.com',
            'password' => bcrypt('password'),
            'role'     => 'manager',
        ]);

        $sales1 = User::create([
            'name'     => 'Vendedor João',
            'email'    => 'joao@crm.com',
            'password' => bcrypt('password'),
            'role'     => 'sales',
        ]);

        $sales2 = User::create([
            'name'     => 'Vendedora Ana',
            'email'    => 'ana@crm.com',
            'password' => bcrypt('password'),
            'role'     => 'sales',
        ]);

        $salesUserIds = [$admin->id, $manager->id, $sales1->id, $sales2->id];

        // ── Dados fictícios ────────────────────────────────────────
        $primeiroNomes = [
            'Carlos',
            'Fernanda',
            'Ricardo',
            'Patricia',
            'Eduardo',
            'Juliana',
            'Marcelo',
            'Camila',
            'Rodrigo',
            'Beatriz',
            'Felipe',
            'Larissa',
            'Gustavo',
            'Natalia',
            'Thiago',
            'Amanda',
            'Bruno',
            'Vanessa',
            'Leonardo',
            'Priscila',
            'Andre',
            'Renata',
            'Diego',
            'Mariana',
            'Rafael',
            'Claudia',
            'Fabio',
            'Luciana',
            'Henrique',
            'Daniela',
            'Lucas',
            'Aline',
            'Vinicius',
            'Tatiana',
            'Matheus',
            'Leticia',
            'Gabriel',
            'Cristina',
            'Arthur',
            'Elaine',
        ];

        $ultimoNomes = [
            'Silva',
            'Santos',
            'Oliveira',
            'Souza',
            'Rodrigues',
            'Ferreira',
            'Alves',
            'Pereira',
            'Lima',
            'Gomes',
            'Costa',
            'Ribeiro',
            'Martins',
            'Carvalho',
            'Araujo',
            'Melo',
            'Barbosa',
            'Cardoso',
            'Nascimento',
            'Moura',
            'Cavalcanti',
            'Teixeira',
            'Castro',
            'Campos',
            'Monteiro',
            'Rocha',
            'Nunes',
            'Correia',
            'Mendes',
            'Dias',
        ];

        $empresas = [
            'Tech Solutions',
            'Grupo Inovação',
            'Comercial Norte',
            'Alpha Sistemas',
            'Beta Consultoria',
            'Omega Digital',
            'Nexus Tecnologia',
            'Sigma Logística',
            'Delta Engenharia',
            'Prime Soluções',
            'Apex Serviços',
            'Vortex TI',
            'Matrix Consultoria',
            'Fusion Digital',
            'Stellar Negócios',
            'Quantum Tech',
            'Polaris Sistemas',
            'Orion Logística',
            'Atlas Comercial',
            'Zenith Engenharia',
            'Nova Empresa',
            'Capital Ventures',
            'MegaSoft',
            'InfoTech',
            'DataBridge',
            'CloudBase',
            'SmartFlow',
            'NetWork',
            'ProSolve',
            'TechHub',
        ];

        $dominios = [
            '.com.br',
            '.net.br',
            '.com',
            '.com.br',
            '.net.br',
        ];

        $dealTitles = [
            'Proposta Comercial',
            'Renovação de Contrato',
            'Expansão de Licenças',
            'Implementação de Sistema',
            'Consultoria Estratégica',
            'Suporte Premium',
            'Projeto Piloto',
            'Contrato Anual',
            'Upgrade de Plano',
            'Parceria Estratégica',
            'Manutenção Preventiva',
            'Treinamento de Equipe',
            'Integração de APIs',
            'Migração de Dados',
            'Auditoria de Sistemas',
        ];

        $stages = ['prospecting', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];
        $stageWeights = [20, 20, 15, 35, 10];

        $interactionNotes = [
            'Ligação realizada, cliente demonstrou interesse.',
            'E-mail de follow-up enviado.',
            'Reunião presencial agendada.',
            'Proposta enviada por e-mail.',
            'Cliente solicitou ajuste no valor.',
            'Demo do produto realizada com sucesso.',
            'Cliente em fase de avaliação interna.',
            'Contrato revisado e enviado para assinatura.',
            'Ligação de retorno, aguardando decisão do cliente.',
            'WhatsApp enviado com materiais complementares.',
            'Cliente pediu prazo de 15 dias para resposta.',
            'Reunião com diretoria do cliente realizada.',
            'Objeções mapeadas e respondidas.',
            'Negociação em andamento, desconto solicitado.',
            'Check-in mensal realizado.',
            'Cliente confirmou recebimento da proposta.',
            'Visita técnica agendada.',
            'Retorno após férias do decisor.',
            'Aprovação interna do cliente em andamento.',
            'Contrato assinado e enviado para financeiro.',
        ];

        $now        = Carbon::now();
        $oneYearAgo = Carbon::now()->subYear();
        $nowTs      = $now->timestamp;
        $yearAgoTs  = $oneYearAgo->timestamp;

        // ── Gerar 1000 clientes em lotes de 100 ───────────────────
        $this->command->info('Criando 1000 clientes...');

        $customerRows = [];
        $usedEmails   = [];

        for ($i = 1; $i <= 1000; $i++) {
            $firstName = $primeiroNomes[array_rand($primeiroNomes)];
            $lastName  = $ultimoNomes[array_rand($ultimoNomes)];
            $empresa   = $empresas[array_rand($empresas)];
            $dominio   = strtolower(str_replace(' ', '', $empresa)) . $dominios[array_rand($dominios)];
            $userId    = $salesUserIds[array_rand($salesUserIds)];
            $createdAt = Carbon::createFromTimestamp(rand($yearAgoTs, $nowTs));

            // Garante e-mail único
            $emailBase = strtolower($firstName . '.' . $lastName . $i);
            $email     = $emailBase . '@' . $dominio;

            $customerRows[] = [
                'user_id'    => $userId,
                'name'       => $firstName . ' ' . $lastName,
                'email'      => $email,
                'phone'      => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'company'    => $empresa,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Insert em lotes de 100
            if (count($customerRows) === 100) {
                DB::table('customers')->insert($customerRows);
                $customerRows = [];
                $this->command->info("  {$i} clientes inseridos...");
            }
        }

        if (!empty($customerRows)) {
            DB::table('customers')->insert($customerRows);
        }

        // ── Busca IDs gerados ──────────────────────────────────────
        $customerRecords = DB::table('customers')
            ->select('id', 'user_id', 'created_at')
            ->get();

        // ── Gerar deals (~3 por cliente) ──────────────────────────
        $this->command->info('Criando deals...');

        $dealRows = [];
        foreach ($customerRecords as $customer) {
            $customerTs = Carbon::parse($customer->created_at)->timestamp;
            $numDeals   = rand(2, 5);

            for ($d = 0; $d < $numDeals; $d++) {
                $dealTs = rand($customerTs, $nowTs);
                $dealDt = Carbon::createFromTimestamp($dealTs);
                $stage  = $this->weightedRandom($stages, $stageWeights);

                $dealRows[] = [
                    'customer_id' => $customer->id,
                    'user_id'     => $customer->user_id,
                    'title'       => $dealTitles[array_rand($dealTitles)],
                    'value'       => rand(500, 50000) + (rand(0, 99) / 100),
                    'stage'       => $stage,
                    'created_at'  => $dealDt,
                    'updated_at'  => $dealDt,
                ];

                if (count($dealRows) === 200) {
                    DB::table('deals')->insert($dealRows);
                    $dealRows = [];
                }
            }
        }

        if (!empty($dealRows)) {
            DB::table('deals')->insert($dealRows);
        }

        $totalDeals = DB::table('deals')->count();
        $this->command->info("  {$totalDeals} deals inseridos.");

        // ── Gerar 10.000 interações em lotes de 500 ───────────────
        $this->command->info('Criando 10.000 interações...');

        $customerIds  = $customerRecords->pluck('id')->toArray();
        $customerMap  = $customerRecords->keyBy('id'); // para pegar user_id e created_at
        $interRows    = [];
        $totalCreated = 0;

        while ($totalCreated < 10000) {
            $customerId = $customerIds[array_rand($customerIds)];
            $customer   = $customerMap[$customerId];
            $customerTs = Carbon::parse($customer->created_at)->timestamp;
            $interTs    = rand($customerTs, $nowTs);
            $interDt    = Carbon::createFromTimestamp($interTs);

            $interRows[] = [
                'customer_id' => $customerId,
                'user_id'     => $customer->user_id,
                'note'        => $interactionNotes[array_rand($interactionNotes)],
                'created_at'  => $interDt,
                'updated_at'  => $interDt,
            ];

            $totalCreated++;

            if (count($interRows) === 500) {
                DB::table('interactions')->insert($interRows);
                $interRows = [];
                $this->command->info("  {$totalCreated} interações inseridas...");
            }
        }

        if (!empty($interRows)) {
            DB::table('interactions')->insert($interRows);
        }

        $this->command->info('✅ Seeder concluído!');
        $this->command->table(
            ['Tabela', 'Total'],
            [
                ['users',        DB::table('users')->count()],
                ['customers',    DB::table('customers')->count()],
                ['deals',        DB::table('deals')->count()],
                ['interactions', DB::table('interactions')->count()],
            ]
        );
    }

    private function weightedRandom(array $items, array $weights): string
    {
        $rand       = rand(1, array_sum($weights));
        $cumulative = 0;

        foreach ($items as $i => $item) {
            $cumulative += $weights[$i];
            if ($rand <= $cumulative) {
                return $item;
            }
        }

        return $items[0];
    }
}
