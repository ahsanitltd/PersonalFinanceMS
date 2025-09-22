<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;

use App\Models\Investment;
use App\Models\InvestmentLog;
use App\Models\JobEarning;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{

    public function dashboard()
    {
        $userId = Auth::id() ?? 1;
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $now = Carbon::now();

        // Investments
        $investmentsQuery = Investment::where('user_id', $userId);
        $assets = (float) $investmentsQuery->sum('amount_invested');
        $yourDue = (float) $investmentsQuery->sum('your_due');
        $partnerDue = (float) $investmentsQuery->sum('partner_due');
        $totalDue = $yourDue + $partnerDue;
        $netWorth = $assets + $partnerDue - $yourDue;

        // Job Earnings
        $monthlyIncome = (float) JobEarning::whereBetween('created_at', [$monthStart, now()])
            ->where('is_paid', true)->sum('amount');

        $dailyIncome = (float) JobEarning::whereDate('created_at', $today)
            ->where('is_paid', true)->sum('amount');

        $unpaidJobEarnings = (float) JobEarning::where('is_paid', false)->sum('amount');

        // Expenses
        $monthlyExpenses = (float) InvestmentLog::where('user_id', $userId)
            ->whereBetween('created_at', [$monthStart, now()])
            ->whereIn('type', ['loss', 'due_payment'])
            ->sum('amount');

        $dailyExpenses = (float) InvestmentLog::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->whereIn('type', ['loss', 'due_payment'])
            ->sum('amount');

        // Income vs Expense trend
        $incomeTrend = JobEarning::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(amount) as income')
        )
            ->where('is_paid', true)
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        $expenseTrend = InvestmentLog::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(amount) as expense')
        )
            ->where('user_id', $userId)
            ->whereIn('type', ['loss', 'due_payment'])
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        $months = $incomeTrend->keys()->merge($expenseTrend->keys())->unique()->sort();

        $incomeVsExpense = [];
        foreach ($months as $month) {
            $incomeVsExpense[$month] = [
                'income' => $incomeTrend[$month]->income ?? 0,
                'expense' => $expenseTrend[$month]->expense ?? 0,
            ];
        }

        // Top Expense Categories
        $topExpenses = InvestmentLog::where('user_id', $userId)
            ->whereIn('type', ['loss', 'due_payment'])
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->orderByDesc('total')->get();

        // Aging Buckets
        $agingBuckets = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
        ];

        $pendingDues = Investment::where('user_id', $userId)
            ->where('your_due', '>', 0)
            ->get();

        foreach ($pendingDues as $due) {
            $updatedAt = $due->updated_at instanceof Carbon
                ? $due->updated_at
                : Carbon::parse($due->updated_at);

            $diff = $updatedAt->diffInDays($now);

            if ($diff <= 30) {
                $agingBuckets['0-30'] += $due->your_due;
            } elseif ($diff <= 60) {
                $agingBuckets['31-60'] += $due->your_due;
            } elseif ($diff <= 90) {
                $agingBuckets['61-90'] += $due->your_due;
            } else {
                $agingBuckets['90+'] += $due->your_due;
            }
        }

        return view('admin.dashboard', compact(
            'assets',
            'netWorth',
            'monthlyIncome',
            'monthlyExpenses',
            'dailyIncome',
            'dailyExpenses',
            'yourDue',
            'partnerDue',
            'totalDue',
            'unpaidJobEarnings',
            'incomeVsExpense',
            'expenseTrend',
            'topExpenses',
            'agingBuckets'
        ));
    }



    function company()
    {
        return view('admin.company.index');
    }

    function investment()
    {
        return view('admin.investment.index');
    }

    function investmentPartner()
    {
        return view('admin.investmentPartner.index');
    }

    function jobEarning()
    {
        return view('admin.jobEarning.index');
    }

    //
}
