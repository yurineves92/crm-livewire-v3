<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Cabeçalho --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Olá, <span class="font-semibold text-gray-700">{{ auth()->user()->name }}</span>!
                    Perfil:
                    <span @class([ 'inline-block px-2 py-0.5 text-xs font-bold rounded-full uppercase' , 'bg-red-100 text-red-700'=> auth()->user()->role === 'admin',
                        'bg-yellow-100 text-yellow-700' => auth()->user()->role === 'manager',
                        'bg-blue-100 text-blue-700' => auth()->user()->role === 'sales',
                        ])>
                        {{ auth()->user()->role }}
                    </span>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                {{-- Filtro de período --}}
                <select wire:model.live="period"
                    class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 py-2">
                    <option value="7days">Últimos 7 dias</option>
                    <option value="30days">Últimos 30 dias</option>
                    <option value="3months">Últimos 3 meses</option>
                    <option value="6months">Últimos 6 meses</option>
                    <option value="1year">Último ano</option>
                </select>

                {{-- Exportar --}}
                <button wire:click="exportCustomers"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                    ⬇ Clientes CSV
                </button>
                <button wire:click="exportDeals"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                    ⬇ Negócios CSV
                </button>

                <a href="{{ route('customers.create') }}" wire:navigate
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    + Novo Cliente
                </a>
            </div>
        </div>

        {{-- Cards KPI --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between min-h-[120px]">
                <p class="text-sm text-gray-500 font-medium">Novos Clientes</p>
                <p class="text-3xl font-black text-blue-600 mt-2 truncate">{{ $totalCustomers }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between min-h-[120px]">
                <p class="text-sm text-gray-500 font-medium">Negócios no Período</p>
                <p class="text-3xl font-black text-purple-600 mt-2 truncate">{{ $totalDeals }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between min-h-[120px]">
                <p class="text-sm text-gray-500 font-medium">Pipeline Ativo</p>
                <p class="text-xl font-black text-yellow-500 mt-2 break-words leading-tight">
                    R$ {{ number_format($pipeline, 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between min-h-[120px]">
                <p class="text-sm text-gray-500 font-medium">Fechados (Ganhos)</p>
                <p class="text-xl font-black text-green-600 mt-2 break-words leading-tight">
                    R$ {{ number_format($closedWon, 2, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Aviso de escopo --}}
        @if(auth()->user()->role === 'sales')
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">
            📌 Você está visualizando apenas seus próprios clientes e negócios.
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700">
            👁️ Você está visualizando os dados de <span class="font-bold">toda a equipe</span>.
        </div>
        @endif

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Gráfico de barras — Receita mensal (últimos 12 meses) --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-bold text-gray-700 mb-4">Receita Fechada — Últimos 12 Meses</h3>
                <div wire:ignore>
                    <canvas id="chartBar" height="120"></canvas>
                </div>
            </div>

            {{-- Gráfico de rosca — Negócios por estágio --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-bold text-gray-700 mb-4">Negócios por Estágio</h3>
                <div wire:ignore>
                    <canvas id="chartDoughnut" height="200"></canvas>
                </div>
                {{-- Legenda manual --}}
                <ul class="mt-4 space-y-1 text-xs text-gray-600">
                    @foreach($stageData as $label => $count)
                    <li class="flex justify-between">
                        <span>{{ $label }}</span>
                        <span class="font-bold">{{ $count }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

        </div>

    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const monthLabels = @json($monthLabels);
    const monthValues = @json($monthValues);
    const stageLabels = @json(array_keys($stageData));
    const stageCounts = @json(array_values($stageData));

    function initCharts() {
        // Destroi instâncias anteriores se existirem
        ['chartBar', 'chartDoughnut'].forEach(id => {
            const existing = Chart.getChart(id);
            if (existing) existing.destroy();
        });

        new Chart(document.getElementById('chartBar'), {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Receita Fechada (R$)',
                    data: monthValues,
                    backgroundColor: 'rgba(37, 99, 235, 0.7)',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: val => 'R$ ' + val.toLocaleString('pt-BR')
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartDoughnut'), {
            type: 'doughnut',
            data: {
                labels: stageLabels,
                datasets: [{
                    data: stageCounts,
                    backgroundColor: ['#3B82F6', '#EAB308', '#F97316', '#22C55E', '#EF4444'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '65%',
            }
        });
    }

    // Roda no carregamento normal E após wire:navigate
    document.addEventListener('livewire:navigated', initCharts);
</script>