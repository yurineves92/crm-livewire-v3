# CRM Livewire

Sistema de CRM (Customer Relationship Management) desenvolvido com **Laravel** e **Livewire**, voltado para gestão de clientes, negócios e equipes de vendas.

---

## Funcionalidades

### Autenticação
- Login com e-mail e senha
- Opção "Lembrar de mim"
- Recuperação de senha

### Dashboard
- Visão geral de métricas em tempo real:
  - **Total de Clientes**
  - **Total de Negócios**
  - **Pipeline Ativo** (valor em R$)
  - **Fechados (Ganhos)** (valor em R$)
- Filtro por período: Últimos 7 dias, 30 dias, 3 meses, 6 meses, 1 ano
- Gráfico de **Receita Fechada — Últimos 12 Meses**
- Gráfico de **Negócios por Estágio** (Prospecção, Proposta, Negociação, Ganho, Perdido)
- Exportação de dados em **CSV** (Clientes e Negócios)

### Gestão de Usuários *(Admin)*
- Criação, edição e exclusão de usuários
- Busca por nome ou e-mail
- Paginação
- Perfis: **Admin**, **Manager**, **Sales**

### Clientes
- Cadastro, edição e exclusão de clientes
- Busca por nome ou e-mail com paginação
- Página de detalhes com Deals e Interações vinculadas

### Deals (Negócios)
- Criação de negócios vinculados ao cliente
- Estágios: Prospecção, Proposta, Negociação, Ganho, Perdido
- Valor em R$

### Interações
- Registro de interações por cliente
- Histórico cronológico

### Perfil
- Atualização de nome e e-mail
- Alteração de senha
- Exclusão de conta

---

## Tecnologias

- **PHP 8.1+** / **Laravel**
- **Livewire 4**
- **Livewire Volt** (formulários de perfil)
- **Blade Templates**
- **Tailwind CSS**
- **Chart.js** (gráficos do dashboard)
- **SQLite** (banco de dados)

---

## Instalação

### Pré-requisitos
- PHP >= 8.1 com extensões `pdo_sqlite` e `sqlite3` habilitadas
- Composer
- Node.js & NPM

### Passos

1. Clone o repositório

   `git clone https://github.com/seu-usuario/crm-livewire.git`
   `cd crm-livewire`

2. Instale as dependências PHP

   `composer install`

3. Instale as dependências JS

   `npm install && npm run build`

4. Configure o ambiente

   `cp .env.example .env`
   `php artisan key:generate`

5. Configure o `.env` para SQLite

   `DB_CONNECTION=sqlite`

6. Crie o arquivo do banco

   `touch database/database.sqlite`

7. Rode as migrations com seed

   `php artisan migrate --seed`

8. Inicie o servidor

   `php artisan serve`

Acesse: http://localhost:8000

---

## Credenciais Padrão

| Nome          | E-mail          | Senha    | Perfil  |
|---------------|-----------------|----------|---------|
| Admin Master  | admin@crm.com   | password | Admin   |
| Gerente Silva | manager@crm.com | password | Manager |
| Vendedor João | joao@crm.com    | password | Sales   |
| Vendedora Ana | ana@crm.com     | password | Sales   |

O seed cria **1.000 clientes**, **~3.500 deals** e **10.000 interações** para testes.

---

## Permissões por Perfil

| Funcionalidade              | Admin | Manager | Sales |
|-----------------------------|:-----:|:-------:|:-----:|
| Ver dados de toda a equipe  | ✅    | ✅      | ❌    |
| Gerenciar usuários          | ✅    | ❌      | ❌    |
| Exportar CSV                | ✅    | ✅      | ❌    |
| Criar/editar clientes       | ✅    | ✅      | ✅    |
| Excluir clientes            | ✅    | ✅      | ⚠️ só os próprios |
| Excluir usuários            | ✅    | ❌      | ❌    |

---

## Licença

Este projeto está sob a licença [MIT](LICENSE).
