<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentLogController;
use App\Http\Controllers\InvestmentPartnerController;
use App\Http\Controllers\JobEarningController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('api-user');
});

Route::resource('api-company-data', CompanyController::class);
Route::resource('api-investment-partner-data', InvestmentPartnerController::class);

Route::resource('api-investment-data', InvestmentController::class);
Route::resource('api-investment-log-data', InvestmentLogController::class);


Route::resource('api-job-earning-data', JobEarningController::class);
