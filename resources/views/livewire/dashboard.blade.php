<div class="page">
    <div class="page-container">

        {{-- Cabeçalho --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Olá, <span class="font-semibold text-slate-700">{{ auth()->user()->name }}</span>!
                    <span @class([
                        'badge ml-1 uppercase',
                        'badge-rose' => auth()->user()->role === 'admin',
                        'badge-amber' => auth()->user()->role === 'manager',
                        'badge-blue' => auth()->user()->role === 'sales',
                    ])>
                        {{ auth()->user()->role }}
                    </span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select wire:model.live="period" class="select w-auto py-2">
                    @foreach (\App\Livewire\Dashboard::PERIODS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <button wire:click="exportCustomers" class="btn-secondary">Clientes CSV</button>
                <button wire:click="exportDeals" class="btn-secondary">Negócios CSV</button>

                <a href="{{ route('customers.create') }}" wire:navigate class="btn-primary">+ Novo Cliente</a>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $kpis = [
                    ['label' => 'Novos Clientes', 'value' => number_format($totalCustomers, 0, ',', '.'), 'accent' => 'text-brand-600'],
                    ['label' => 'Negócios no Período', 'value' => number_format($totalDeals, 0, ',', '.'), 'accent' => 'text-violet-600'],
                    ['label' => 'Pipeline Ativo', 'value' => 'R$ ' . number_format($pipeline, 2, ',', '.'), 'accent' => 'text-amber-500'],
                    ['label' => 'Fechados (Ganhos)', 'value' => 'R$ ' . number_format($closedWon, 2, ',', '.'), 'accent' => 'text-emerald-600'],
                ];
            @endphp

            @foreach ($kpis as $kpi)
                <div class="card p-6 flex flex-col justify-between min-h-[116px]">
                    <p class="text-sm font-medium text-slate-500">{{ $kpi['label'] }}</p>
                    <p class="mt-2 text-2xl font-extrabold leading-tight break-words {{ $kpi['accent'] }}">
                        {{ $kpi['value'] }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Escopo de visualização --}}
        <div @class([
            'rounded-xl border p-4 text-sm',
            'bg-brand-50 border-brand-200 text-brand-800' => auth()->user()->isSales(),
            'bg-amber-50 border-amber-200 text-amber-800' => ! auth()->user()->isSales(),
        ])>
            @if (auth()->user()->isSales())
                Você está visualizando apenas seus próprios clientes e negócios.
            @else
                Você está visualizando os dados de <span class="font-bold">toda a equipe</span>.
            @endif
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-6">
                <h2 class="card-title mb-4">Receita Fechada — Últimos 12 Meses</h2>
                <div wire:ignore>
                    <canvas id="chartBar" height="120"></canvas>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="card-title mb-4">Negócios por Estágio</h2>
                <div wire:ignore>
                    <canvas id="chartDoughnut" height="200"></canvas>
                </div>
                <ul class="mt-4 space-y-1 text-xs text-slate-600">
                    @foreach ($stageData as $label => $count)
                        <li class="flex justify-between">
                            <span>{{ $label }}</span>
                            <span class="font-bold">{{ $count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endassets

    @script
        <script>
            const palette = ['#3b6df6', '#eab308', '#f97316', '#22c55e', '#ef4444'];

            const renderCharts = ({ monthLabels, monthValues, stageData }) => {
                ['chartBar', 'chartDoughnut'].forEach((id) => Chart.getChart(id)?.destroy());

                new Chart(document.getElementById('chartBar'), {
                    type: 'bar',
                    data: {
                        labels: monthLabels,
                        datasets: [{
                            label: 'Receita Fechada (R$)',
                            data: monthValues,
                            backgroundColor: 'rgba(59, 109, 246, 0.75)',
                            borderRadius: 6,
                            borderSkipped: false,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { ticks: { callback: (value) => 'R$ ' + value.toLocaleString('pt-BR') } },
                        },
                    },
                });

                new Chart(document.getElementById('chartDoughnut'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(stageData),
                        datasets: [{
                            data: Object.values(stageData),
                            backgroundColor: palette,
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        cutout: '65%',
                    },
                });
            };

            renderCharts(@json(['monthLabels' => $monthLabels, 'monthValues' => $monthValues, 'stageData' => $stageData]));

            // O filtro de período redesenha os gráficos (os canvases usam wire:ignore).
            $wire.on('charts-refreshed', (payload) => renderCharts(Array.isArray(payload) ? payload[0] : payload));
        </script>
    @endscript
</div>
