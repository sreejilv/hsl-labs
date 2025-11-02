@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-chart-bar me-2"></i>Financial Reports</h1>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>${{ number_format($reportsData['monthly_revenue'], 2) }}</h4>
                                    <p class="mb-0">Monthly Revenue</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-arrow-up fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>${{ number_format($reportsData['monthly_expenses'], 2) }}</h4>
                                    <p class="mb-0">Monthly Expenses</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-arrow-down fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>${{ number_format($reportsData['monthly_profit'], 2) }}</h4>
                                    <p class="mb-0">Monthly Profit</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>${{ number_format($reportsData['year_to_date_revenue'], 2) }}</h4>
                                    <p class="mb-0">YTD Revenue</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Revenue Sources Chart -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>Revenue Sources</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" style="height: 300px;"></canvas>
                            <div class="mt-3">
                                @foreach($reportsData['top_revenue_sources'] as $source)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $source['source'] }}</span>
                                    <div>
                                        <span class="badge bg-primary">${{ number_format($source['amount'], 2) }}</span>
                                        <span class="text-muted">({{ $source['percentage'] }}%)</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trends Chart -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-line-chart me-2"></i>Monthly Trends</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="trendsChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Financial Report -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detailed Financial Report</h5>
                                <div>
                                    <button class="btn btn-light btn-sm" onclick="window.print()">
                                        <i class="fas fa-print me-1"></i>Print
                                    </button>
                                    <button class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel me-1"></i>Export Excel
                                    </button>
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Month</th>
                                            <th>Revenue</th>
                                            <th>Expenses</th>
                                            <th>Net Profit</th>
                                            <th>Profit Margin</th>
                                            <th>Growth</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportsData['monthly_trends'] as $index => $trend)
                                        @php
                                            $profit = $trend['revenue'] - $trend['expenses'];
                                            $margin = $trend['revenue'] > 0 ? ($profit / $trend['revenue']) * 100 : 0;
                                            $prevProfit = $index > 0 ? ($reportsData['monthly_trends'][$index-1]['revenue'] - $reportsData['monthly_trends'][$index-1]['expenses']) : $profit;
                                            $growth = $prevProfit > 0 ? (($profit - $prevProfit) / $prevProfit) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $trend['month'] }} 2025</strong></td>
                                            <td class="text-success">${{ number_format($trend['revenue'], 2) }}</td>
                                            <td class="text-danger">${{ number_format($trend['expenses'], 2) }}</td>
                                            <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>${{ number_format($profit, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $margin >= 70 ? 'success' : ($margin >= 50 ? 'warning' : 'danger') }}">
                                                    {{ number_format($margin, 1) }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if($index > 0)
                                                    <span class="badge bg-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                                        {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th>Total/Average</th>
                                            <th class="text-success">${{ number_format(array_sum(array_column($reportsData['monthly_trends'], 'revenue')), 2) }}</th>
                                            <th class="text-danger">${{ number_format(array_sum(array_column($reportsData['monthly_trends'], 'expenses')), 2) }}</th>
                                            <th class="text-primary">
                                                <strong>${{ number_format(array_sum(array_column($reportsData['monthly_trends'], 'revenue')) - array_sum(array_column($reportsData['monthly_trends'], 'expenses')), 2) }}</strong>
                                            </th>
                                            <th>
                                                @php
                                                    $totalRevenue = array_sum(array_column($reportsData['monthly_trends'], 'revenue'));
                                                    $totalExpenses = array_sum(array_column($reportsData['monthly_trends'], 'expenses'));
                                                    $avgMargin = $totalRevenue > 0 ? (($totalRevenue - $totalExpenses) / $totalRevenue) * 100 : 0;
                                                @endphp
                                                <span class="badge bg-primary">{{ number_format($avgMargin, 1) }}%</span>
                                            </th>
                                            <th>-</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Sources Pie Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'doughnut',
    data: {
        labels: @json(array_column($reportsData['top_revenue_sources'], 'source')),
        datasets: [{
            data: @json(array_column($reportsData['top_revenue_sources'], 'amount')),
            backgroundColor: [
                '#007bff',
                '#28a745',
                '#ffc107',
                '#dc3545',
                '#6f42c1'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Monthly Trends Line Chart
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
const trendsChart = new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($reportsData['monthly_trends'], 'month')),
        datasets: [
            {
                label: 'Revenue',
                data: @json(array_column($reportsData['monthly_trends'], 'revenue')),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            },
            {
                label: 'Expenses',
                data: @json(array_column($reportsData['monthly_trends'], 'expenses')),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection