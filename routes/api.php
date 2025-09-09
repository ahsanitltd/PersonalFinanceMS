<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvestmentEntityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::resource('api-company-data', CompanyController::class);
Route::resource('api-investment-entity-data', InvestmentEntityController::class);
