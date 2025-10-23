<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;

use App\Models\Investment;
use App\Models\InvestmentLog;
use App\Models\InvestmentPartner;
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

        // Fetch all partners for this user
        $partners = InvestmentPartner::where('user_id', $userId)->get();

        // Initialize arrays to hold partner-wise data
        $partnerMonthlyIncome = [];
        $partnerMonthlyProfit = [];
        $partnerMonthlyExpenses = [];
        $partnerDailyIncome = [];
        $partnerTotalDue = [];
        $partnerTotalInvested = [];

        foreach ($partners as $partner) {
            $partnerId = $partner->id;

            // Investments by this partner
            $partnerInvestmentsQuery = Investment::where('user_id', $userId)
                ->where('investment_partner_id', $partnerId);

            // Total invested by partner
            $partnerTotalInvested[$partner->name] = (float) $partnerInvestmentsQuery->sum('amount_invested');

            // Partner owes you (partner_due)
            $partnerTotalDue[$partner->name] = (float) $partnerInvestmentsQuery->sum('partner_due');

            // Monthly income for this partner (sum of profit + due_payment)
            $partnerMonthlyIncome[$partner->name] = (float) InvestmentLog::where('user_id', $userId)
                ->whereHas('investment', fn($q) => $q->where('investment_partner_id', $partnerId))
                ->whereBetween('created_at', [$monthStart, $now])
                ->whereIn('type', ['profit', 'due_payment'])
                ->sum('amount');

            // Monthly profit only
            $partnerMonthlyProfit[$partner->name] = (float) InvestmentLog::where('user_id', $userId)
                ->whereHas('investment', fn($q) => $q->where('investment_partner_id', $partnerId))
                ->whereBetween('created_at', [$monthStart, $now])
                ->where('type', 'profit')
                ->sum('amount');

            // Monthly expenses (investment + loss)
            $partnerMonthlyExpenses[$partner->name] = (float) InvestmentLog::where('user_id', $userId)
                ->whereHas('investment', fn($q) => $q->where('investment_partner_id', $partnerId))
                ->whereBetween('created_at', [$monthStart, $now])
                ->whereIn('type', ['investment', 'loss'])
                ->sum('amount');

            // Today's income (profit + due_payment)
            $partnerDailyIncome[$partner->name] = (float) InvestmentLog::where('user_id', $userId)
                ->whereHas('investment', fn($q) => $q->where('investment_partner_id', $partnerId))
                ->whereDate('created_at', $today)
                ->whereIn('type', ['profit', 'due_payment'])
                ->sum('amount');
        }

        // -- The rest of your original method unchanged, returning all totals, trends, etc --

        // Investments totals
        $investmentsQuery = Investment::where('user_id', $userId);
        $assets = (float) $investmentsQuery->sum('amount_invested');
        $yourDue = (float) $investmentsQuery->sum('your_due');
        $partnerDue = (float) $investmentsQuery->sum('partner_due');
        $totalDue = $yourDue + $partnerDue;
        $netWorth = $assets + $partnerDue - $yourDue;

        // Job Income + Investment income (totals)
        $monthlyJobIncome = (float) JobEarning::whereBetween('created_at', [$monthStart, $now])
            ->where('is_paid', true)
            ->sum('amount');

        $monthlyInvestmentIncome = (float) InvestmentLog::where('user_id', $userId)
            ->whereBetween('created_at', [$monthStart, $now])
            ->whereIn('type', ['profit', 'due_payment'])
            ->sum('amount');

        $monthlyProfitOnly = (float) InvestmentLog::where('user_id', $userId)
            ->whereBetween('created_at', [$monthStart, $now])
            ->where('type', 'profit')
            ->sum('amount');

        $monthlyIncome = $monthlyJobIncome + $monthlyInvestmentIncome;

        // Daily Income (totals)
        $dailyJobIncome = (float) JobEarning::whereDate('created_at', $today)
            ->where('is_paid', true)
            ->sum('amount');

        $dailyInvestmentIncome = (float) InvestmentLog::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->whereIn('type', ['profit', 'due_payment'])
            ->sum('amount');

        $dailyProfitOnly = (float) InvestmentLog::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->where('type', 'profit')
            ->sum('amount');

        $dailyIncome = $dailyJobIncome + $dailyInvestmentIncome;

        // Monthly Expenses (total)
        $monthlyExpenses = (float) InvestmentLog::where('user_id', $userId)
            ->whereBetween('created_at', [$monthStart, $now])
            ->whereIn('type', ['investment', 'loss'])
            ->sum('amount');

        // Daily Expenses (total)
        $dailyExpenses = (float) InvestmentLog::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->whereIn('type', ['investment', 'loss'])
            ->sum('amount');

        // Income trend (Job + Investment)
        $jobIncomeTrend = JobEarning::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(amount) as income')
        )
            ->where('is_paid', true)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $investmentIncomeTrend = InvestmentLog::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(amount) as income')
        )
            ->where('user_id', $userId)
            ->whereIn('type', ['profit', 'due_payment'])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $incomeTrend = collect();

        foreach ($jobIncomeTrend as $month => $data) {
            $incomeTrend[$month] = ['income' => $data->income];
        }

        $incomeTrend = $incomeTrend instanceof \Illuminate\Support\Collection ? $incomeTrend->toArray() : $incomeTrend;

        foreach ($investmentIncomeTrend as $month => $data) {
            if (isset($incomeTrend[$month])) {
                $incomeTrend[$month]['income'] += $data->income;
            } else {
                $incomeTrend[$month] = ['income' => $data->income];
            }
        }

        $incomeTrend = collect($incomeTrend);

        // Expense trend
        $expenseTrend = InvestmentLog::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(amount) as expense')
        )
            ->where('user_id', $userId)
            ->whereIn('type', ['investment', 'loss'])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Merge income vs expense
        $months = $incomeTrend->keys()->merge($expenseTrend->keys())->unique()->sort();

        $incomeVsExpense = [];
        foreach ($months as $month) {
            $incomeVsExpense[$month] = [
                'income' => $incomeTrend[$month]['income'] ?? 0,
                'expense' => $expenseTrend[$month]->expense ?? 0,
            ];
        }

        // Top Expense Categories
        $topExpenses = InvestmentLog::where('user_id', $userId)
            ->whereIn('type', ['investment', 'loss'])
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

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

        // Unpaid job earnings
        $unpaidJobEarnings = (float) JobEarning::where('is_paid', false)->sum('amount');

        return view('admin.dashboard', compact(
            'assets',
            'netWorth',
            'monthlyIncome',
            'monthlyJobIncome',
            'monthlyInvestmentIncome',
            'monthlyProfitOnly',
            'dailyIncome',
            'dailyJobIncome',
            'dailyInvestmentIncome',
            'dailyProfitOnly',
            'monthlyExpenses',
            'dailyExpenses',
            'yourDue',
            'partnerDue',
            'totalDue',
            'unpaidJobEarnings',
            'incomeVsExpense',
            'expenseTrend',
            'topExpenses',
            'agingBuckets',
            // Partner wise data
            'partnerMonthlyIncome',
            'partnerMonthlyProfit',
            'partnerMonthlyExpenses',
            'partnerDailyIncome',
            'partnerTotalDue',
            'partnerTotalInvested'
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
