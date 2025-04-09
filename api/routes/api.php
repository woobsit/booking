<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

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

Route::middleware(['api'])->prefix('v1')->group(function () {
    //Admin Login
    //Route::post('/admin-login', [AdminAuthController::class, 'adminLogin']);
    //User Login
    //Route::post('/user-login', [UserAuthController::class, 'userLogin']);

    Route::get('/show-all-bookings', [BookingController::class, 'index']);

    //Book vehicle
    Route::post('/book', [BookingController::class, 'store']);
});

/*User routes*/
Route::middleware(['auth:api', 'scope:user'])->prefix('v1')->group(function () {

    //User Logout
    //Route::post('/user-logout', [UserAuthController::class, 'userLogout']);

    //get user bookings
    Route::get('/my-bookings', [BookingController::class, 'userBookings']);
    //Book vehicle
    //Route::post('/book', [BookingController::class, 'store']);
    //show booking
    Route::get('/show-booking', [BookingController::class, 'show']);
    //update booking
    Route::patch('/update-booking', [BookingController::class, 'update']);
    //cancel booking
    Route::get('/cancel-booking', [BookingController::class, 'cancel']);
});


Route::middleware(['auth:admin', 'scope:admin'])->prefix('v1')->group(function () {
    //Admin Logout
    //Route::post('/admin-logout', [AdminAuthController::class, 'adminLogout']);

    //show all bookings
    // Route::get('/show-bookings', [BookingController::class, 'index']);
});
