<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticController;

Route::get('/admin/statistics', [StatisticController::class, 'index'])->name('statistics.index');


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
