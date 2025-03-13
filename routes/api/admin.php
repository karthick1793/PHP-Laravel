<?php

use App\Http\Controllers\Admin\Activity\TokenTransactionController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\UploadProfessorController;
use App\Http\Controllers\Admin\Delivery\DeliveryController;
use App\Http\Controllers\Admin\DropDown\DropDownController;
use App\Http\Controllers\Admin\ManageToken\ManageTokenController;
use App\Http\Controllers\Admin\Reports\MilkTransactionController;
use Illuminate\Support\Facades\Route;

//Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/create', [AuthController::class, 'create']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/resend-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/change-password', [AuthController::class, 'changePassword']);

Route::post('/upload-data/csv', [UploadProfessorController::class, 'uploadCsv']);

Route::middleware(['auth.admin'])->group(function () {
    //DropDown
    Route::get('/quarters/list', [DropDownController::class, 'listQuarters']);
    Route::get('/professors/list', [DropDownController::class, 'listProfessors']);

    //ManageTokens
    Route::post('/manage-token/professor/list', [ManageTokenController::class, 'professorList']);
    Route::post('/manage-token/token/update', [ManageTokenController::class, 'updateProfessorToken']);
    Route::post('/manage-token/download', [ManageTokenController::class, 'downloadProfessorWithTokens']);

    //Delivery
    Route::post('/delivery/count', [DeliveryController::class, 'listDeliveryCountsByDate']);
    Route::post('/delivery/list', [DeliveryController::class, 'listDeliveryDataOfDate']);
    Route::post('/delivery/list/download', [DeliveryController::class, 'downloadDeliveryDataOfDate']);
    Route::post('/delivery/update', [DeliveryController::class, 'updateBookingStatus']);
    Route::post('/delivery/bulk/update', [DeliveryController::class, 'bulkBookingUpdate']);

    //Activity
    Route::post('/activity/token-transaction/log', [TokenTransactionController::class, 'listTransactionHistory']);
    Route::post('/activity/token-transaction/log/download', [TokenTransactionController::class, 'downloadTokenTransactions']);

    //Reports
    Route::post('/reports/milk-transaction/list', [MilkTransactionController::class, 'listMilkTransactionHistory']);
    Route::post('/reports/milk-transaction/download', [MilkTransactionController::class, 'downloadMilkTransactions']);

    //Auth
    Route::get('/logout', [AuthController::class, 'logout']);
});
