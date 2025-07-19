<?php

use App\Http\Controllers\Api\QuoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Quote API Routes
Route::prefix('quotes')->group(function () {
    // Get random quote from external API
    Route::get('/random', [QuoteController::class, 'getRandomQuote'])->name('api.quotes.random');
    
    // Get cached quote
    Route::get('/cached', [QuoteController::class, 'getCachedQuote'])->name('api.quotes.cached');
    
    // Refresh quote (clear cache and fetch new)
    Route::post('/refresh', [QuoteController::class, 'refreshQuote'])->name('api.quotes.refresh');

});