# CRM Livewire

MVP de CRM (clientes, negócios e interações) feito com **Laravel 13 + Livewire 3 + Tailwind CSS 3**, banco **SQLite** e autenticação do **Laravel Breeze**.

---

## Stack

| Camada    | Tecnologia                                              |
|-----------|---------------------------------------------------------|
| Backend   | PHP 8.3, Laravel 13                                      |
| UI        | Livewire 3 + Volt (telas de auth/perfil), Blade          |
| Estilo    | Tailwind CSS 3 (+ `@tailwindcss/forms`), Vite 8          |
| Gráficos  | Chart.js 4 (via `@assets` do Livewire)                   |
| Banco     | SQLite                                                   |
| Testes    | PHPUnit 12 (`php artisan test`)                          |

---

## Instalação

Pré-requisitos: PHP >= 8.3 com `pdo_sqlite`, Composer e Node.js 20+.

```bash
composer install
```

```bash
cp .env.example .env && php artisan key:generate
```

```bash
npm install && npm run build
```

```bash
php artisan migrate:fresh --seed
```

```bash
php artisan serve
```

Acesse http://localhost:8000 (a raiz redireciona para `/dashboard`, que exige login).

Para desenvolver com hot reload, use `npm run dev` em um terminal e `php artisan serve` em outro
(ou `composer dev`, que sobe servidor + fila + logs + Vite juntos). Se as telas aparecerem sem
estilo depois de encerrar o `npm run dev`, apague o arquivo `public/hot`.

---

## Usuários do seed

Todos com a senha `password`:

| Nome          | E-mail          | Perfil  |
|---------------|-----------------|---------|
| Admin Master  | admin@crm.com   | admin   |
| Gerente Silva | manager@crm.com | manager |
| Vendedor João | joao@crm.com    | sales   |
| Vendedora Ana | ana@crm.com     | sales   |

---

## Funcionalidades

- **Dashboard** — KPIs (novos clientes, negócios, pipeline ativo e total ganho) com filtro de
  período (7 dias a 1 ano), gráfico de receita fechada dos últimos 12 meses, distribuição por
  estágio e exportação CSV de clientes e negócios.
- **Clientes** — CRUD completo, busca por nome/e-mail/empresa com paginação e contagem de negócios.
- **Negócios** — criação, edição e exclusão dentro da ficha do cliente, com estágios
  `Prospecção → Proposta → Negociação → Ganho/Perdido`.
- **Interações** — histórico cronológico de contatos por cliente.
- **Usuários** *(admin)* — CRUD de usuários com perfis e busca.
- **Perfil** — atualização de dados, troca de senha e exclusão da conta (Breeze/Volt).

### Permissões

A regra vive em `App\Livewire\Concerns\ScopesToUser`: **admin** e **manager** enxergam os dados de
toda a equipe; **sales** enxerga (e altera) apenas os registros em que é o `user_id` responsável —
tentativas fora do escopo retornam `403`. Só o **admin** acessa a gestão de usuários.

---

## Banco de dados

### Migrations

```
0001_01_01_000000_create_users_table.php          users (+ role), password_reset_tokens, sessions
0001_01_01_000001_create_cache_table.php          cache, cache_locks
0001_01_01_000002_create_jobs_table.php           jobs, job_batches, failed_jobs
2026_06_18_100000_create_customers_table.php      customers
2026_06_18_100100_create_deals_table.php          deals
2026_06_18_100200_create_interactions_table.php   interactions
```

As tabelas do CRM usam `cascadeOnDelete` (excluir um cliente remove seus negócios e interações;
excluir um usuário remove sua carteira) e índices compostos em `user_id`, `customer_id` e `stage`
com `created_at`, que são as colunas usadas pelo dashboard e pelas listagens.

### Seeders

| Seeder            | O que faz                                                              |
|-------------------|------------------------------------------------------------------------|
| `UserSeeder`      | Os 4 usuários da tabela acima (idempotente, usa `firstOrCreate`)        |
| `CrmDemoSeeder`   | Base de demonstração: 40 clientes, 1–4 negócios e 1–5 interações cada   |
| `DatabaseSeeder`  | Chama os dois e imprime um resumo dos totais                           |

Os dados de demonstração são gerados por factories (`CustomerFactory`, `DealFactory`,
`InteractionFactory`) e distribuídos ao longo dos últimos 12 meses, para os gráficos ficarem
populados. O volume é configurável:

```bash
SEED_CUSTOMERS=200 php artisan migrate:fresh --seed
```

---

## Testes

```bash
php artisan test
```

Cobrem CRUD de clientes, negócios e interações, o escopo por perfil (403), os KPIs e exportações do
dashboard, a gestão de usuários, os seeders e a autenticação/perfil que vieram do Breeze.

---

## Estrutura

```
app/
  Livewire/            Dashboard, CustomerList, CustomerForm, CustomerShow, UserManagement
  Livewire/Concerns/   ScopesToUser (visibilidade por perfil)
  Models/              User, Customer, Deal, Interaction
resources/
  css/app.css          design system em @layer components (.card, .btn-*, .input, .badge-*, .table)
  views/livewire/      telas do CRM
tailwind.config.js     paleta "brand" e fonte Figtree
database/
  factories/ migrations/ seeders/
tests/
  Feature/ Unit/
```

O CSS concentra os padrões visuais em classes de componente (`.card`, `.btn-primary`, `.input`,
`.badge-green`, `.table`…), então as views ficam curtas e uma mudança de estilo acontece em um
lugar só — inclusive nos componentes do Breeze (`resources/views/components`), que foram
realinhados a esse mesmo design system.

---

## Licença

MIT.
