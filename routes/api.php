<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TimerApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Timer API endpoint - protected by API key
Route::middleware('api.key')->group(function () {
    Route::get('/timer/current', [TimerApiController::class, 'current']);
});

