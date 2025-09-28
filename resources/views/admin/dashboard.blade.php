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
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: transform 0.15s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgb(0 0 0 / 0.15);
        }

        .stat-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-value {
            font-size: 1.0rem;
            font-weight: 700;
            color: #212529;
        }

        .small-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 6px;
            line-height: 1.3;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        .text-success {
            color: #198754 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-info {
            color: #0dcaf0 !important;
        }

        /* Chart cards fix height */
        .chart-card {
            min-height: 280px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
        }
    </style>
@endsection

@section('main-content')
    <div class="container-fluid py-4">
        <h4 class="mb-4 fw-bold">📊 Financial Dashboard</h4>

        {{-- First Row: Net Worth, Monthly Income, Profit, Expenses --}}
        <div class="row g-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-coins text-warning"></i> Net Worth</div>
                    <div class="stat-value text-success">{{ number_format($netWorth ?? 0, 2) }} BDT</div>
                    <div class="small-text">Assets: {{ number_format($assets ?? 0, 2) }} BDT</div>
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
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-chart-line text-success"></i> Monthly Profit Only</div>
                    <div class="stat-value text-success">{{ number_format($monthlyProfitOnly ?? 0, 2) }} BDT</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-arrow-down text-danger"></i> Monthly Expenses</div>
                    <div class="stat-value text-danger">{{ number_format($monthlyExpenses ?? 0, 2) }} BDT</div>
                </div>
            </div>
        </div>

        {{-- Second Row: Today's Income, Profit, Expenses --}}
        <div class="row g-4 mt-2">
            <div class="col-lg-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-dollar-sign text-primary"></i> Today's Income</div>
                    <div class="stat-value text-primary">{{ number_format($dailyIncome ?? 0, 2) }} BDT</div>
                    <div class="small-text">
                        Job: {{ number_format($dailyJobIncome ?? 0, 2) }}<br>
                        Investment: {{ number_format($dailyInvestmentIncome ?? 0, 2) }}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-money-bill-wave text-success"></i> Today's Profit Only</div>
                    <div class="stat-value text-success">{{ number_format($dailyProfitOnly ?? 0, 2) }} BDT</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-wallet text-danger"></i> Today's Expenses</div>
                    <div class="stat-value text-danger">{{ number_format($dailyExpenses ?? 0, 2) }} BDT</div>
                    <div class="small-text">
                        @if (($dailyExpenses ?? 0) > ($dailyIncome ?? 0))
                            <span class="text-danger fw-semibold">⚠ Over Budget</span>
                        @else
                            <span class="text-success fw-semibold">✅ Within Budget</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Third Row: Dues and Invested --}}
        <div class="row g-4 mt-2">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-hand-holding-usd text-danger"></i> You Owe (Due)</div>
                    <div class="stat-value text-danger">{{ number_format($yourDue ?? 0, 2) }} BDT</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-handshake text-success"></i> Partner Owes You</div>
                    <div class="stat-value text-success">{{ number_format($partnerDue ?? 0, 2) }} BDT</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-piggy-bank text-info"></i> Total Invested</div>
                    <div class="stat-value text-info">{{ number_format($assets ?? 0, 2) }} BDT</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-file-invoice-dollar text-warning"></i> Total Due</div>
                    <div class="stat-value text-warning">{{ number_format($totalDue ?? 0, 2) }} BDT</div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row mt-4 g-4">
            <div class="col-4">
                <div class="stat-card">
                    <h5 class="fw-semibold">📆 Due Aging Report</h5>
                    <ul class="list-group list-group-flush mt-3">
                        @foreach ($agingBuckets as $range => $amount)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $range }} days</span>
                                <strong>{{ number_format($amount, 2) }} BDT</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="chart-card">
                    <h5 class="mb-3 fw-semibold">Top Expense Categories</h5>
                    <canvas id="topExpensePieChart"></canvas>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="chart-card">
                    <h5 class="mb-3 fw-semibold">You Owe vs They Owe</h5>
                    <canvas id="youVsTheyPieChart"></canvas>
                </div>
            </div>

        </div>
        <div class="row mt-4 g-4">
            <div class="col-lg-6 col-md-12">
                <div class="chart-card">
                    <h5 class="mb-3 fw-semibold">📈 Income vs Expenses (Monthly)</h5>
                    <canvas id="incomeVsExpenseChart"></canvas>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="chart-card">
                    <h5 class="mb-3 fw-semibold">Due Aging</h5>
                    <canvas id="dueAgingBarChart"></canvas>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('custom-js')
    <script src="https://kit.fontawesome.com/a2d9c503f8.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Income vs Expenses Chart
            const ctxIE = document.getElementById('incomeVsExpenseChart');
            if (ctxIE) {
                new Chart(ctxIE.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($incomeVsExpense)) !!},
                        datasets: [{
                                label: 'Income',
                                data: {!! json_encode(array_column($incomeVsExpense, 'income')) !!},
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Expenses',
                                data: {!! json_encode(array_column($incomeVsExpense, 'expense')) !!},
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
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

            // Top Expenses Pie
            const ctxPie = document.getElementById('topExpensePieChart');
            if (ctxPie) {
                new Chart(ctxPie.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($topExpenses->pluck('type')) !!},
                        datasets: [{
                            data: {!! json_encode($topExpenses->pluck('total')) !!},
                            backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc',
                                '#d2d6de'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // You vs They Doughnut
            const ctxYouThey = document.getElementById('youVsTheyPieChart');
            if (ctxYouThey) {
                new Chart(ctxYouThey.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['You Owe', 'They Owe'],
                        datasets: [{
                            data: [{{ $yourDue }}, {{ $partnerDue }}],
                            backgroundColor: ['rgba(255, 99, 132, 0.7)', 'rgba(0, 123, 255, 0.7)'],
                            borderColor: ['rgba(255, 99, 132, 1)', 'rgba(0, 123, 255, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '50%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Due Aging Bar
            const ctxAgingBar = document.getElementById('dueAgingBarChart');
            if (ctxAgingBar) {
                new Chart(ctxAgingBar.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($agingBuckets)) !!},
                        datasets: [{
                            label: 'Due Amount (BDT)',
                            data: {!! json_encode(array_values($agingBuckets)) !!},
                            backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545'],
                            borderColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
