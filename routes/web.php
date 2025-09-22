<?php

use App\Http\Controllers\front\FrontendController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [FrontendController::class, 'dashboard'])->name('dashboard');
    Route::get('/company', [FrontendController::class, 'company'])->name('company');

    Route::get('/investment', [FrontendController::class, 'investment'])->name('investment');
    Route::get('/investment-partner', [FrontendController::class, 'investmentPartner'])->name('investment.partner');

    Route::get('/job-earning', [FrontendController::class, 'jobEarning'])->name('job.earning');
});


// Route::get('/test', function () {
//     return '
//     <form method="POST" action="/test-post">
//         ' . csrf_field() . '
//         <button type="submit">Submit Test</button>
//     </form>';
// });

// Route::post('/test-post', function (Request $request) {
//     Log::debug('Session data: ', session()->all());
//     return 'CSRF passed! Session working ✅';
// });
