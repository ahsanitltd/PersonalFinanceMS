<?php

namespace App\Http\Controllers;

use App\Actions\FilterModel;
use App\Http\Requests\InvestmentRequest;
use App\Models\Investment;
use App\Models\InvestmentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, FilterModel $filterModel)
    {
        try {
            $query = $filterModel->handle(Investment::with('user', 'investmentPartner'), $request);

            return successResponse('Showing All Data', $query->orderBy('id', 'DESC')->paginate(10));
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvestmentRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = Auth::id() ?? 1;

            if ($request->profit_type == 'fixed') {
                $data['partner_due'] = $request->partner_due + $request->profit_value;
            }

            $allData = Investment::create($data);

            InvestmentLog::create([
                "investment_id" => $allData->id,
                'type' => 'investment',
                "paid_by" => 16,
                "amount" => $request->amount_invested,
                'user_id' => $data['user_id']
            ]);


            DB::commit();
            return successResponse('This data has been Created', $allData);
        } catch (\Exception $e) {
            DB::rollback();
            return errorResponse($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = Investment::find($id);
            if (empty($data)) {
                throw new \Exception('Unable to Find This Task');
            }

            return successResponse('Open Modal', $data);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InvestmentRequest $request, string $id)
    {
        try {
            $taskData = Investment::find($id);
            if (empty($taskData)) {
                throw new \Exception('Unable to find this data');
            }

            $data = $request->validated();
            if ($request->profit_type == 'fixed') {
                $data['partner_due'] = $request->partner_due + $request->profit_value;
            }

            $taskData->update($data);

            return successResponse('This data has been Updated', $taskData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $ids = !is_array($request->ids) ? explode(',', $request->ids) : $request->ids;
            Investment::whereIn('id', $ids)->delete();
            return successResponse('This data has been Destroyed');
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }
}
