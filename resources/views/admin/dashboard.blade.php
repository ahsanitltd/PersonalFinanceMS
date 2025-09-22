@extends('admin.master.app')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/css/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jqvmap.min.css') }}">
    <style>
        .stat-card {
            background: #fff;
            border-radius: 6px;
            padding: 18px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .stat-title {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            margin-top: 6px;
        }

        .chart-card {
            height: 380px;
        }

        .small-text {
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* For pie / doughnut etc. */
        .chart-half-height {
            height: 240px;
        }
    </style>
@endsection

@section('main-content')
    <div class="container-fluid py-3">
        <h4 class="mb-4">📊 Financial Dashboard with More Charts</h4>

        {{-- Summary Cards (same as before) --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-title">Net Worth</div>
                    <div class="stat-value text-success">{{ number_format($netWorth ?? 0, 2) }} BDT</div>
                    <div class="small-text">Assets: {{ number_format($assets ?? 0, 2) }} BDT</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-title">Monthly Income</div>
                    <div class="stat-value text-primary">{{ number_format($monthlyIncome ?? 0, 2) }} BDT</div>
                    <div class="small-text">Unpaid: {{ number_format($unpaidJobEarnings ?? 0, 2) }} BDT</div>
                </div>
            </div>

            <div class="col-lg-3 col-md=6 mb-3">
                <div class="stat-card">
                    <div class="stat-title">Monthly Expenses</div>
                    <div class="stat-value text-danger">{{ number_format($monthlyExpenses ?? 0, 2) }} BDT</div>
                </div>
            </div>

            <div class="col-lg-3 col-md=6 mb-3">
                <div class="stat-card">
                    <div class="stat-title">Today's Income / Expenses</div>
                    <div class="stat-value">
                        <span class="text-primary">{{ number_format($dailyIncome ?? 0, 2) }}</span>
                        &nbsp;/&nbsp;
                        <span class="text-danger">{{ number_format($dailyExpenses ?? 0, 2) }}</span>
                    </div>
                    <div class="small-text">
                        Status:
                        @if (($monthlyExpenses ?? 0) > ($monthlyIncome ?? 0))
                            <span class="text-danger">⚠ Over Budget</span>
                        @else
                            <span class="text-success">✅ Within Budget</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- More summary / due cards --}}
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-title">You Owe (Your Due)</div>
                    <div class="stat-value text-danger">{{ number_format($yourDue ?? 0, 2) }} BDT</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-title">Partner Owes You</div>
                    <div class="stat-value text-success">{{ number_format($partnerDue ?? 0, 2) }} BDT</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-title">Total Invested</div>
                    <div class="stat-value text-info">{{ number_format($assets ?? 0, 2) }} BDT</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-title">Total Due</div>
                    <div class="stat-value text-warning">{{ number_format($totalDue ?? 0, 2) }} BDT</div>
                </div>
            </div>
        </div>

        {{-- Charts main area --}}
        <div class="row">
            {{-- Line / Bar Chart for Income vs Expense --}}
            <div class="col-lg-8 mb-3">
                <div class="stat-card chart-card">
                    <h5 class="mb-3">Income vs Expense (Monthly)</h5>
                    <canvas id="incomeVsExpenseChart" style="width:100%; height:320px;"></canvas>
                </div>
            </div>

            {{-- Top Expense Pie --}}
            <div class="col-lg-4 mb-3">
                <div class="stat-card chart-half-height">
                    <h5 class="mb-3">Top Expense Categories</h5>
                    <canvas id="topExpensePieChart" style="width:100%; height:240px;"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- New: Pie Chart showing portion of Your Due vs Partner Due --}}
            <div class="col-lg-6 mb-3">
                <div class="stat-card chart-half-height">
                    <h5 class="mb-3">You Owe vs They Owe (Pie)</h5>
                    <canvas id="youVsTheyPieChart" style="width:100%; height:240px;"></canvas>
                </div>
            </div>

            {{-- New: Bar Chart showing aging buckets --}}
            <div class="col-lg-6 mb-3">
                <div class="stat-card chart-half-height">
                    <h5 class="mb-3">Due Aging Bar Chart</h5>
                    <canvas id="dueAgingBarChart" style="width:100%; height:240px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Aging Report text / list --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="stat-card">
                    <h5>📆 Due Aging Report</h5>
                    <ul class="list-group list-group-flush mt-2">
                        @foreach ($agingBuckets as $range => $amount)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $range }} days</span>
                                <strong>{{ number_format($amount, 2) }} BDT</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
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
                            backgroundColor: [
                                '#f56954',
                                '#00a65a',
                                '#f39c12',
                                '#00c0ef',
                                '#3c8dbc',
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

            // You vs They Pie Chart
            const ctxYouThey = document.getElementById('youVsTheyPieChart');
            if (ctxYouThey) {
                new Chart(ctxYouThey.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['You Owe', 'They Owe'],
                        datasets: [{
                            data: [{!! json_encode($yourDue) !!}, {!! json_encode($partnerDue) !!}],
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

            // Due Aging Bar Chart
            const ctxAgingBar = document.getElementById('dueAgingBarChart');
            if (ctxAgingBar) {
                new Chart(ctxAgingBar.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($agingBuckets)) !!},
                        datasets: [{
                            label: 'Due Amount (BDT)',
                            data: {!! json_encode(array_values($agingBuckets)) !!},
                            backgroundColor: [
                                '#28a745', '#ffc107', '#fd7e14', '#dc3545'
                            ],
                            borderColor: [
                                '#28a745', '#ffc107', '#fd7e14', '#dc3545'
                            ],
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
