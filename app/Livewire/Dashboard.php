<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Deal;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Dashboard extends Component
{
    public string $period = '30days';

    public function updatedPeriod(): void
    {
        // rerender automático
    }

    private function startDate(): Carbon
    {
        return match ($this->period) {
            '7days'   => Carbon::now()->subDays(7),
            '30days'  => Carbon::now()->subDays(30),
            '3months' => Carbon::now()->subMonths(3),
            '6months' => Carbon::now()->subMonths(6),
            '1year'   => Carbon::now()->subYear(),
            default   => Carbon::now()->subDays(30),
        };
    }

    public function exportCustomers(): StreamedResponse
    {
        $user  = auth()->user();
        $query = $user->role === 'sales'
            ? Customer::where('user_id', $user->id)
            : Customer::query();

        $customers = $query->with('deals')->get();

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nome', 'Email', 'Telefone', 'Empresa', 'Total Negócios', 'Criado em'], ';');

            foreach ($customers as $c) {
                fputcsv($handle, [
                    $c->id,
                    $c->name,
                    $c->email,
                    $c->phone ?? '',
                    $c->company ?? '',
                    $c->deals->count(),
                    $c->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 'clientes_' . now()->format('Ymd_His') . '.csv');
    }

    public function exportDeals(): StreamedResponse
    {
        $user  = auth()->user();
        $query = $user->role === 'sales'
            ? Deal::where('user_id', $user->id)
            : Deal::query();

        $deals = $query->with('customer')->get();

        return response()->streamDownload(function () use ($deals) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Título', 'Cliente', 'Empresa', 'Valor', 'Estágio', 'Criado em'], ';');

            $stageLabels = [
                'prospecting' => 'Prospecção',
                'proposal'    => 'Proposta',
                'negotiation' => 'Negociação',
                'closed_won'  => 'Ganho',
                'closed_lost' => 'Perdido',
            ];

            foreach ($deals as $d) {
                fputcsv($handle, [
                    $d->id,
                    $d->title,
                    $d->customer->name ?? '',
                    $d->customer->company ?? '',
                    number_format($d->value, 2, ',', '.'),
                    $stageLabels[$d->stage] ?? $d->stage,
                    $d->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 'negócios_' . now()->format('Ymd_His') . '.csv');
    }

    public function render()
    {
        $user      = auth()->user();
        $startDate = $this->startDate();

        $customerQuery = $user->role === 'sales'
            ? Customer::where('user_id', $user->id)
            : Customer::query();

        $dealQuery = $user->role === 'sales'
            ? Deal::where('user_id', $user->id)
            : Deal::query();

        // KPIs no período
        $totalCustomers = (clone $customerQuery)->where('created_at', '>=', $startDate)->count();
        $totalDeals     = (clone $dealQuery)->where('created_at', '>=', $startDate)->count();
        $pipeline       = (clone $dealQuery)->where('created_at', '>=', $startDate)
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value');
        $closedWon      = (clone $dealQuery)->where('created_at', '>=', $startDate)
            ->where('stage', 'closed_won')->sum('value');

        // Gráfico de barras — receita fechada por mês (últimos 12 meses)
        $months      = collect();
        $monthLabels = [];
        $monthValues = [];

        for ($i = 11; $i >= 0; $i--) {
            $date        = Carbon::now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M/y');
            $monthValues[] = (clone $dealQuery)
                ->where('stage', 'closed_won')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('value');
        }

        // Gráfico de rosca — negócios por estágio no período
        $stageData = [
            'Prospecção' => (clone $dealQuery)->where('created_at', '>=', $startDate)->where('stage', 'prospecting')->count(),
            'Proposta'   => (clone $dealQuery)->where('created_at', '>=', $startDate)->where('stage', 'proposal')->count(),
            'Negociação' => (clone $dealQuery)->where('created_at', '>=', $startDate)->where('stage', 'negotiation')->count(),
            'Ganho'      => (clone $dealQuery)->where('created_at', '>=', $startDate)->where('stage', 'closed_won')->count(),
            'Perdido'    => (clone $dealQuery)->where('created_at', '>=', $startDate)->where('stage', 'closed_lost')->count(),
        ];

        return view('livewire.dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalDeals'     => $totalDeals,
            'pipeline'       => $pipeline,
            'closedWon'      => $closedWon,
            'monthLabels'    => $monthLabels,
            'monthValues'    => $monthValues,
            'stageData'      => $stageData,
        ]);
    }
}
