<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DayController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\StopController;

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

// TripController
Route::get('get-trips', [TripController::class, 'getTrips']);
Route::get('trip-detail/{slug}', [TripController::class, 'getTripBySlug']);
Route::post('create-trip', [TripController::class, 'store']);
Route::put('/trip-detail/{slug}/edit', [TripController::class, 'edit']);
Route::delete('trip-detail/{slug}', [TripController::class, 'delete']);

// DayController
Route::get('day-detail/{slug}/{date}', [DayController::class, 'getDayByDate']);
Route::post('day-detail/{slug}/{date}/upload-images', [DayController::class, 'uploadImages']);

// ImageController
Route::post('upload-images', [ImageController::class, 'uploadImages']);

// StopController
Route::post('/stops', [StopController::class, 'store']);
Route::get('stops/{day_id}', [StopController::class, 'getStops']);
Route::get('all-stops', [StopController::class, 'getAllStops']);
Route::delete('/stops/{id}', [StopController::class, 'destroy']);
