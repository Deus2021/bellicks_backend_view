<?php

use App\Http\Controllers\Admin\CapitalController;
use App\Http\Controllers\Admin\LoanTypeController;
use App\Http\Controllers\LoanOfficer\CollectionController;
use App\Http\Controllers\LoanOfficer\CustomerController;
use App\Http\Controllers\LoanOfficer\CustomerMessageController;
use App\Http\Controllers\LoanOfficer\LoanController;
use App\Http\Controllers\LoanOfficer\ReportsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'activity']], function () {
    Route::prefix('l')->group(function () {
        Route::apiResources([
            'customer' => CustomerController::class,
            'loan' => LoanController::class,
            'loan_type' => LoanTypeController::class,
            'customer_message' => CustomerMessageController::class,
            'reports' => ReportsController::class
        ]);

        Route::post('loan_declined', [LoanController::class, 'LoanDeclined']);
        Route::post('loan_approved', [LoanController::class, 'LoanApproved']);
        Route::get('loan_capital', [CapitalController::class, 'show']);
        Route::get('collections', [CollectionController::class, 'index']);
        Route::get('loan-customer-details', [CustomerController::class, 'getCustomer']);
        Route::get('customer-progress', [CustomerController::class, 'getCustomersData']);
        Route::get('customer-with-loans', [CustomerController::class, 'customerWithLoans']);
        Route::get('customer-with-out-loans', [CustomerController::class, 'customerWithOutLoans']);
        Route::post('register-customer', [CustomerController::class, 'temporaryCustomers']); 
    });
});