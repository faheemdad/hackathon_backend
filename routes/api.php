<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/get-available-slots', [CalendarController::class, 'getAvailableSlots']);
Route::post('/book-slot', [CalendarController::class, 'bookSlot']);





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


