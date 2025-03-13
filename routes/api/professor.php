<?php

use App\Http\Controllers\Professor\Activity\TransactionController;
use App\Http\Controllers\Professor\Auth\AuthController;
use App\Http\Controllers\Professor\Bookings\BookingListController;
use App\Http\Controllers\Professor\Home\HomeController;
use App\Http\Controllers\Professor\Scanner\ScannerController;
use Illuminate\Support\Facades\Route;

//Auth
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::middleware(['auth.professor'])->group(function () {
    //Home
    Route::get('/home', [HomeController::class, 'home']);
    Route::get('/home/book-milk', [HomeController::class, 'bookMilk']);
    //Scanner
    Route::post('/buy-milk', [ScannerController::class, 'buyMilk']);
    //Bookings
    Route::get('/bookings/list', [BookingListController::class, 'bookingList']);
    Route::post('/bookings/cancel', [BookingListController::class, 'cancelBooking']);
    //Activity
    Route::post('/transaction-history/list', [TransactionController::class, 'transactionList']);

    //Auth
    Route::get('/logout', [AuthController::class, 'logout']);
});
