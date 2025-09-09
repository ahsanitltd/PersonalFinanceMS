<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FilterModel
{
    public function handle(Builder $query, Request $request, array $columns = []): Builder
    {
        if (!empty($request->columns)) {
            $query->select($request->columns);
        }

        $model = $query->getModel();
        $columns = $columns ?: $model->getFillable();

        $filterFields = array_filter(
            $request->only($columns),
            fn($v) => $v !== null && $v !== ''
        );

        // 1. Apply field-specific LIKE filters (AND logic)
        if (!empty($filterFields)) {
            $query->whereAll(
                array_map(
                    fn($col, $val) => fn($q) => $q->where($col, 'LIKE', "%{$val}%"),
                    array_keys($filterFields),
                    $filterFields
                )
            );
        }

        // 2. Apply global search across all fillable (OR logic)
        if ($request->filled('search')) {
            $query->whereAny($columns, 'LIKE', '%' . $request->input('search') . '%');
        }

        return $query;
    }
}
