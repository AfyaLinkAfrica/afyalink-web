<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');
