<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

Route::get('/facilities/{facility}/report', [ReportController::class, 'create'])->name('facilities.report.create');
Route::post('/facilities/{facility}/report', [ReportController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('facilities.report.store');
