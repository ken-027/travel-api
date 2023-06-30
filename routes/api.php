<?php

use App\Http\Controllers\Api\v1\Admin\TourController as AdminTourController;
use App\Http\Controllers\Api\v1\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\v1\TravelController;
use App\Http\Controllers\HeathCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::get('healthcheck', HeathCheckController::class);

Route::get('travels', [TravelController::class, 'index'])->name('travels');
Route::get('travels/{travel:slug}/tours', [TourController::class, 'index'])->name('travel.slug.tours');
Route::prefix('admin')->as('admin.')->group(function () {
    Route::post('travels', [AdminTravelController::class, 'store'])->name('store.travel');
    Route::put('travels/{travel}', [AdminTravelController::class, 'update'])->name('update.travel')->whereUuid('travel');
    Route::post('travels/{travel}/tours', [AdminTourController::class, 'store'])->name('store.tour')->whereUuid('travel');
});

Route::prefix('auth')->as('auth.')->group(function () {
    Route::post('login', LoginController::class)->name('login');
});
