<?php

namespace App\Http\Controllers;

use App\Actions\FilterModel;
use App\Http\Requests\InvestmentLogRequest;
use App\Models\Investment;
use App\Models\InvestmentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvestmentLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, FilterModel $filterModel)
    {
        // dd($request->all());
        try {
            $query = $filterModel->handle(InvestmentLog::with('user')->where('investment_id', $request->investment_id), $request);
            return successResponse('Showing All Data', $query->orderBy('id', 'DESC')->paginate(5));
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvestmentLogRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id() ?? 1;

            $investmentLogData = InvestmentLog::create($data);

            $investmentData = Investment::findorFail($request->investment_id);
            if (!empty($investmentData)) {

                if ($request->type == 'investment') {
                    $updateInvestmentData['your_due'] = ($investmentData->your_due > 0 && $investmentData->your_due - $request->amount > 0) ? $investmentData->your_due - $request->amount : 0;
                    $updateInvestmentData['partner_due'] = $investmentData->partner_due + $request->amount;
                    $updateInvestmentData['amount_invested'] = $investmentData->amount_invested + $request->amount;
                } else
                if (in_array($request->type, ['due_payment', 'profit'])) {

                    $updateInvestmentData['partner_due'] = $investmentData->partner_due - $request->amount;
                } else
                if ($request->type == 'loss') {
                    $updateInvestmentData['status'] = 'closed';
                    $updateInvestmentData['notes'] = '</br> <b>Loss:</b>' . $request->amount . '</br> ' . $investmentData->notes ;
                }

                $investmentData->update($updateInvestmentData);
            }
            DB::commit();
            return successResponse('This data has been Created', $investmentLogData ?? []);
        } catch (\Exception $e) {
            DB::rollback();
            return errorResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            $logs = InvestmentLog::whereIn('id', $ids)->get();

            foreach ($logs as $log) {
                $inv = Investment::findOrFail($log->investment_id);

                $data = [];

                if ($log->type === 'investment') {
                    $data = [
                        'your_due' => $inv->your_due + $log->amount,
                        'partner_due' => $inv->partner_due - $log->amount,
                        'amount_invested' => $inv->amount_invested - $log->amount,
                    ];
                } else 
                if (in_array($log->type, ['due_payment', 'profit'])) {
                    $data['partner_due'] = $inv->partner_due + $log->amount;
                } else 
if ($log->type === 'loss') {
                    $data['status'] = 'active';

                    // Clean the appended loss note from investment notes
                    $escapedAmount = preg_quote((string)$log->amount, '/');
                    $pattern = "/<\/br>\s*<b>Loss:<\/b>\s*{$escapedAmount}\s*<\/br>/i";

                    $data['notes'] = preg_replace($pattern, '', $inv->notes);
                }

                $inv->update($data);
            }

            InvestmentLog::whereIn('id', $ids)->delete();


            return successResponse('This data has been Destroyed');
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }
}
