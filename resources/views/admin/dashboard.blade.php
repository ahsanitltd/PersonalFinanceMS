@extends('admin.master.app')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/css/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jqvmap.min.css') }}">
    <style>
        body {
            background: #f8f9fa;
        }

        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
            margin-bottom: 25px;
            transition: transform 0.15s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgb(0 0 0 / 0.15);
        }

        .stat-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 1rem;
            font-weight: 700;
            color: #212529;
        }

        .small-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 6px;
            line-height: 1.3;
        }

        .small-text.partner-breakdown div {
            margin-bottom: 4px;
        }

        .chart-card {
            min-height: 280px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
        }

        body {
            background-color: #f8f9fa;
        }

        .card-header {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .partner-card {
            min-height: 280px;
        }

        .summary-card h5 {
            font-size: 1.5rem;
        }
    </style>
@endsection

@section('main-content')
    <div class="container-fluid py-4">
        <div class="container py-4">
            <h1 class="mb-4 text-center">Dashboard</h1>

            {{-- Overall Summary --}}
            <div class="row g-3 mb-5">
                <div class="col-md-3">
                    <div class="card summary-card text-white bg-primary">
                        <div class="card-header">Total Assets</div>
                        <div class="card-body">
                            <h5>{{ number_format($assets, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card text-white bg-success">
                        <div class="card-header">Net Worth</div>
                        <div class="card-body">
                            <h5>{{ number_format($netWorth, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card text-white bg-info">
                        <div class="card-header">Monthly Income</div>
                        <div class="card-body">
                            <h5>{{ number_format($monthlyIncome, 2) }}</h5>
                            <small>
                                Job: {{ number_format($monthlyJobIncome, 2) }}<br>
                                Investment: {{ number_format($monthlyInvestmentIncome, 2) }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card text-white bg-warning">
                        <div class="card-header">Monthly Expenses</div>
                        <div class="card-body">
                            <h5>{{ number_format($monthlyExpenses, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Partner Cards --}}
            <h2 class="mb-3">Partner Financial Details</h2>

            <div class="row g-4">
                @forelse ($partnerTotalInvested as $partnerName => $invested)
                    <div class="col-md-4">
                        <div class="card partner-card border-primary h-100 shadow-sm">
                            <div class="card-header bg-primary text-white">{{ $partnerName }}</div>
                            <div class="card-body">
                                <p><strong>Total Invested:</strong> {{ number_format($invested, 2) }}</p>
                                <p><strong>Total Due:</strong> {{ number_format($partnerTotalDue[$partnerName] ?? 0, 2) }}
                                </p>
                                <hr>
                                <p><strong>Monthly Income:</strong>
                                    {{ number_format($partnerMonthlyIncome[$partnerName] ?? 0, 2) }}</p>
                                <p><strong>Monthly Profit:</strong>
                                    {{ number_format($partnerMonthlyProfit[$partnerName] ?? 0, 2) }}</p>
                                <p><strong>Monthly Expenses:</strong>
                                    {{ number_format($partnerMonthlyExpenses[$partnerName] ?? 0, 2) }}</p>
                                <hr>
                                <p><strong>Today's Income:</strong>
                                    {{ number_format($partnerDailyIncome[$partnerName] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">No partners found.</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ===== Summary Row ===== --}}
        <div class="row g-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-coins text-warning"></i> Net Worth</div>
                    <div class="stat-value text-success">{{ number_format($netWorth ?? 0, 2) }} BDT</div>
                    <div class="small-text">Assets: {{ number_format($assets ?? 0, 2) }}</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-arrow-up text-primary"></i> Monthly Income</div>
                    <div class="stat-value text-primary">{{ number_format($monthlyIncome ?? 0, 2) }} BDT</div>
                    <div class="small-text">
                        Job: {{ number_format($monthlyJobIncome ?? 0, 2) }}<br>
                        Investment: {{ number_format($monthlyInvestmentIncome ?? 0, 2) }}<br>
                        Profit: {{ number_format($monthlyProfitOnly ?? 0, 2) }}
                    </div>

                    @if (!empty($partnerIncome) && is_array($partnerIncome))
                        <div class="small-text partner-breakdown mt-2 border-top pt-2">
                            <strong>Partner Income Breakdown:</strong>
                            @foreach ($partnerIncome as $p)
                                <div><strong>{{ $p['partner'] ?? 'N/A' }}:</strong>
                                    {{ number_format($p['amount'] ?? 0, 2) }} BDT</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-chart-line text-success"></i> Monthly Profit</div>
                    <div class="stat-value text-success">{{ number_format($monthlyProfitOnly ?? 0, 2) }} BDT</div>

                    @if (!empty($partnerProfit) && is_array($partnerProfit))
                        <div class="small-text partner-breakdown mt-2 border-top pt-2">
                            <strong>Partner Profit Breakdown:</strong>
                            @foreach ($partnerProfit as $p)
                                <div><strong>{{ $p['partner'] ?? 'N/A' }}:</strong>
                                    {{ number_format($p['amount'] ?? 0, 2) }} BDT</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-arrow-down text-danger"></i> Monthly Expenses</div>
                    <div class="stat-value text-danger">{{ number_format($monthlyExpenses ?? 0, 2) }} BDT</div>

                    @if (!empty($partnerExpense) && is_array($partnerExpense))
                        <div class="small-text partner-breakdown mt-2 border-top pt-2">
                            <strong>Partner Expense Breakdown:</strong>
                            @foreach ($partnerExpense as $p)
                                <div><strong>{{ $p['partner'] ?? 'N/A' }}:</strong>
                                    {{ number_format($p['amount'] ?? 0, 2) }} BDT</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Today Section ===== --}}
        <div class="row g-4 mt-2">
            <div class="col-lg-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-dollar-sign text-primary"></i> Today's Income</div>
                    <div class="stat-value text-primary">{{ number_format($dailyIncome ?? 0, 2) }} BDT</div>
                    <div class="small-text">
                        Job: {{ number_format($dailyJobIncome ?? 0, 2) }}<br>
                        Investment: {{ number_format($dailyInvestmentIncome ?? 0, 2) }}
                    </div>

                    @if (!empty($todayPartnerIncome) && is_array($todayPartnerIncome))
                        <div class="small-text partner-breakdown mt-2 border-top pt-2">
                            <strong>Partner Income Breakdown:</strong>
                            @foreach ($todayPartnerIncome as $p)
                                <div><strong>{{ $p['partner'] ?? 'N/A' }}:</strong>
                                    {{ number_format($p['amount'] ?? 0, 2) }} BDT</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-money-bill-wave text-success"></i> Today's Profit</div>
                    <div class="stat-value text-success">{{ number_format($dailyProfitOnly ?? 0, 2) }} BDT</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-wallet text-danger"></i> Today's Expenses</div>
                    <div class="stat-value text-danger">{{ number_format($dailyExpenses ?? 0, 2) }} BDT</div>
                </div>
            </div>
        </div>

        {{-- ===== Partner-Wise Totals ===== --}}
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-hand-holding-usd text-danger"></i> You Owe (Due)</div>
                    <div class="stat-value text-danger">{{ number_format($yourDue ?? 0, 2) }} BDT</div>

                    @if (!empty($partnerStats) && is_array($partnerStats))
                        <div class="small-text partner-breakdown mt-2 border-top pt-2">
                            <strong>Partner Due Breakdown:</strong>
                            @foreach ($partnerStats as $p)
                                <div><strong>{{ $p['name'] ?? 'N/A' }}:</strong>
                                    {{ number_format($p['your_due'] ?? 0, 2) }} BDT</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-handshake text-success"></i> Partner Owes You</div>
                    <div class="stat-value text-success">{{ number_format($partnerDue ?? 0, 2) }} BDT</div>

                    @if (!empty($partnerStats) && is_array($partnerStats))
                        <div class="small-text partner-breakdown mt-2 border-top pt-2">
                            <strong>Partner Due Breakdown:</strong>
                            @foreach ($partnerStats as $p)
                                <div><strong>{{ $p['name'] ?? 'N/A' }}:</strong>
                                    {{ number_format($p['partner_due'] ?? 0, 2) }} BDT</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-piggy-bank text-info"></i> Total Invested</div>
                    <div class="stat-value text-info">{{ number_format($assets ?? 0, 2) }} BDT</div>

                    @if (!empty($partnerStats) && is_array($partnerStats))
                        <div class="small-text partner-breakdown mt-2 border-top pt-2">
                            <strong>Partner Invested Breakdown:</strong>
                            @foreach ($partnerStats as $p)
                                <div><strong>{{ $p['name'] ?? 'N/A' }}:</strong>
                                    {{ number_format($p['invested'] ?? 0, 2) }} BDT</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>


        {{-- ===== Charts ===== --}}
        <div class="row mt-4 g-4">
            <div class="col-lg-6 col-md-12">
                <div class="chart-card">
                    <h5>📈 Income vs Expense</h5>
                    <canvas id="incomeVsExpenseChart"></canvas>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="chart-card">
                    <h5>📊 Due Aging</h5>
                    <canvas id="dueAgingBarChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Income vs Expense Chart --}}
        <h2 class="my-5 text-center">Income vs Expense Trend (Monthly)</h2>
        <canvas id="incomeExpenseChart" style="max-width: 100%; height: 350px;"></canvas>
    </div>
@endsection

@section('custom-js')
    <script src="https://kit.fontawesome.com/a2d9c503f8.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const incomeCtx = document.getElementById('incomeVsExpenseChart');
            if (incomeCtx && {!! json_encode(isset($incomeVsExpense)) !!}) {
                new Chart(incomeCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($incomeVsExpense ?? [])) !!},
                        datasets: [{
                                label: 'Income',
                                data: {!! json_encode(array_column($incomeVsExpense ?? [], 'income')) !!},
                                backgroundColor: 'rgba(0,123,255,0.7)'
                            },
                            {
                                label: 'Expense',
                                data: {!! json_encode(array_column($incomeVsExpense ?? [], 'expense')) !!},
                                backgroundColor: 'rgba(255,99,132,0.7)'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            const dueCtx = document.getElementById('dueAgingBarChart');
            if (dueCtx && {!! json_encode(isset($agingBuckets)) !!}) {
                new Chart(dueCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($agingBuckets ?? [])) !!},
                        datasets: [{
                            label: 'Due (BDT)',
                            data: {!! json_encode(array_values($agingBuckets ?? [])) !!},
                            backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545']
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>



    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('incomeExpenseChart').getContext('2d');

            // Prepare labels and data from PHP arrays passed from controller
            const incomeVsExpense = @json($incomeVsExpense);

            // Sort months (keys)
            const months = Object.keys(incomeVsExpense).sort();

            const incomeData = months.map(month => incomeVsExpense[month].income ?? 0);
            const expenseData = months.map(month => incomeVsExpense[month].expense ?? 0);

            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                            label: 'Income',
                            data: incomeData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true,
                            tension: 0.3,
                        },
                        {
                            label: 'Expenses',
                            data: expenseData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                            tension: 0.3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Monthly Income vs Expense Trend',
                            font: {
                                size: 18
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                // Include $ sign in y-axis labels
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
