<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipController;

Route::middleware(['auth'])->group(function () {
    // Tipping
    Route::post('/tips/send', [TipController::class, 'send'])->name('tips.send');
});
