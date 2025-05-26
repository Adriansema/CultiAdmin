<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticController;


Route::get('/estadisticas', [StatisticController::class, 'getStatistics']);


Route::middleware('auth:sanctum')->get('/statistics', [StatisticController::class, 'getStatistics']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
