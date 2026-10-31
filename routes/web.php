<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\PrivateAdminPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

Route::get('/facilities/{facility}/report', [ReportController::class, 'create'])->name('facilities.report.create');
Route::post('/facilities/{facility}/report', [ReportController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('facilities.report.store');

// Plain-language search helper. It only turns words into the existing search filters.
Route::get('/assistant', [AssistantController::class, 'show'])->name('assistant.show');
Route::post('/assistant', [AssistantController::class, 'ask'])
    ->middleware('throttle:20,1')
    ->name('assistant.ask');

// Maintainer area. Not linked from any public page. Accounts are created with `php artisan admin:create`.
Route::prefix('admin')->middleware(PrivateAdminPage::class)->group(function () {
    // The route name "login" is what Laravel's auth middleware redirects guests to.
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('admin.login.store');

    Route::middleware('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

        Route::get('reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
        Route::get('reports/{report}', [AdminReportController::class, 'show'])->name('admin.reports.show');
        Route::post('reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('admin.reports.resolve');
    });
});
