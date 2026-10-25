<?php

use App\Http\Controllers\FacilityController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
