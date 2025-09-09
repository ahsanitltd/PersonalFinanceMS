<?php

namespace App\Http\Controllers;

use App\Actions\FilterModel;
use App\Http\Requests\InvestmentEntityRequest;
use App\Models\InvestmentEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\InvestmentEntityResource;

class InvestmentEntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, FilterModel $filterModel)
    {
        try {
            $query = $filterModel->handle(InvestmentEntity::with('user'), $request);

            return successResponse('Showing All Data', $query->orderBy('id', 'DESC')->paginate(10));
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvestmentEntityRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id() ?? 1;
            $allData = InvestmentEntity::create($data);
            return successResponse('This data has been Created', $allData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = InvestmentEntity::find($id);
            if (empty($data)) {
                throw new \Exception('Unable to Find This Task');
            }

            return successResponse('Open Modal', $data);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InvestmentEntityRequest $request, string $id)
    {
        try {
            $taskData = InvestmentEntity::find($id);
            if (empty($taskData)) {
                throw new \Exception('Unable to find this data');
            }

            $taskData->update($request->validated());

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
            InvestmentEntity::whereIn('id', $ids)->delete();
            return successResponse('This data has been Destroyed');
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }
}
