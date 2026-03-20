<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipController;

Route::middleware(['auth'])->group(function () {
    // Tipping — throttle: max 20 gifts per minute per user to prevent spam
    Route::post('/tips/send', [TipController::class, 'send'])
        ->middleware('throttle:20,1')
        ->name('tips.send');
});
