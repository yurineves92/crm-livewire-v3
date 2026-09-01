<?php

namespace App\Livewire;

use App\Livewire\Concerns\ScopesToUser;
use App\Models\Customer;
use App\Models\Deal;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Dashboard extends Component
{
    use ScopesToUser;

    public string $period = '30days';

    /**
     * Períodos disponíveis no filtro.
     *
     * @var array<string, string>
     */
    public const PERIODS = [
        '7days'   => 'Últimos 7 dias',
        '30days'  => 'Últimos 30 dias',
        '3months' => 'Últimos 3 meses',
        '6months' => 'Últimos 6 meses',
        '1year'   => 'Último ano',
    ];

    public function updatedPeriod(): void
    {
        if (! array_key_exists($this->period, self::PERIODS)) {
            $this->period = '30days';
        }

        // Os canvases usam wire:ignore, então os gráficos são redesenhados via evento.
        $this->dispatch('charts-refreshed', ...$this->chartData());
    }

    private function startDate(): Carbon
    {
        return match ($this->period) {
            '7days'   => Carbon::now()->subDays(7),
            '3months' => Carbon::now()->subMonths(3),
            '6months' => Carbon::now()->subMonths(6),
            '1year'   => Carbon::now()->subYear(),
            default   => Carbon::now()->subDays(30),
        };
    }

    /**
     * Dados dos dois gráficos (barras + rosca).
     *
     * @return array{monthLabels: list<string>, monthValues: list<float>, stageData: array<string, int>}
     */
    private function chartData(): array
    {
        $startOfWindow = Carbon::now()->startOfMonth()->subMonths(11);

        $revenueByMonth = $this->scoped(Deal::class)
            ->where('stage', Deal::STAGE_WON)
            ->where('created_at', '>=', $startOfWindow)
            ->get(['created_at', 'value'])
            ->groupBy(fn (Deal $deal) => $deal->created_at->format('Y-m'))
            ->map(fn ($deals) => (float) $deals->sum('value'));

        $monthLabels = [];
        $monthValues = [];

        for ($i = 11; $i >= 0; $i--) {
            $date          = Carbon::now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M/y');
            $monthValues[] = $revenueByMonth->get($date->format('Y-m'), 0.0);
        }

        $counts = $this->scoped(Deal::class)
            ->where('created_at', '>=', $this->startDate())
            ->selectRaw('stage, count(*) as total')
            ->groupBy('stage')
            ->pluck('total', 'stage');

        $stageData = [];

        foreach (Deal::STAGES as $stage => $label) {
            $stageData[$label] = (int) $counts->get($stage, 0);
        }

        return [
            'monthLabels' => $monthLabels,
            'monthValues' => $monthValues,
            'stageData'   => $stageData,
        ];
    }

    public function exportCustomers(): StreamedResponse
    {
        $customers = $this->scoped(Customer::class)->withCount('deals')->get();

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nome', 'Email', 'Telefone', 'Empresa', 'Total Negócios', 'Criado em'], ';');

            foreach ($customers as $customer) {
                fputcsv($handle, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone ?? '',
                    $customer->company ?? '',
                    $customer->deals_count,
                    $customer->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 'clientes_' . now()->format('Ymd_His') . '.csv');
    }

    public function exportDeals(): StreamedResponse
    {
        $deals = $this->scoped(Deal::class)->with('customer')->get();

        return response()->streamDownload(function () use ($deals) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Título', 'Cliente', 'Empresa', 'Valor', 'Estágio', 'Criado em'], ';');

            foreach ($deals as $deal) {
                fputcsv($handle, [
                    $deal->id,
                    $deal->title,
                    $deal->customer->name ?? '',
                    $deal->customer->company ?? '',
                    number_format($deal->value, 2, ',', '.'),
                    $deal->stage_label,
                    $deal->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 'negocios_' . now()->format('Ymd_His') . '.csv');
    }

    public function render()
    {
        $startDate = $this->startDate();

        $totals = $this->scoped(Deal::class)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('count(*) as total')
            ->selectRaw('coalesce(sum(case when stage in (?, ?) then 0 else value end), 0) as pipeline', [Deal::STAGE_WON, Deal::STAGE_LOST])
            ->selectRaw('coalesce(sum(case when stage = ? then value else 0 end), 0) as closed_won', [Deal::STAGE_WON])
            ->first();

        return view('livewire.dashboard', [
            'totalCustomers' => $this->scoped(Customer::class)->where('created_at', '>=', $startDate)->count(),
            'totalDeals'     => (int) $totals->total,
            'pipeline'       => (float) $totals->pipeline,
            'closedWon'      => (float) $totals->closed_won,
            ...$this->chartData(),
        ]);
    }
}
