<?php

use App\Http\Controllers\BranchManager\BankController;
use App\Http\Controllers\BranchManager\CapitalController;
use App\Http\Controllers\BranchManager\CollectionController;
use App\Http\Controllers\BranchManager\CustomerController;
use App\Http\Controllers\BranchManager\ExpensesController;
use App\Http\Controllers\BranchManager\InventoryController;
use App\Http\Controllers\BranchManager\LoansController;
use App\Http\Controllers\BranchManager\ReportsController;
use Illuminate\Support\Facades\Route;


// Route::post('inventory',[InventoryController::class, 'store']);
// Route::apiResource('branch_manager', RoleController::class);

Route::group(['middleware' => ['auth:sanctum', 'activity']], function () {
    Route::prefix('b')->group(function () {
        Route::apiResources([
            'inventories' => InventoryController::class,
            'loans' => LoansController::class,
            'expenses' => ExpensesController::class,
            'customers' => CustomerController::class,
            'to_banks' => BankController::class,
            'reports' => ReportsController::class,
        ]);
        Route::post('loan_declined', [LoansController::class, 'LoanDeclined']);
        Route::post('loan_approved', [LoansController::class, 'LoanApproved']);
        Route::post('loan_rejected', [LoansController::class, 'LoanRejected']);
        Route::get('wait_approval', [LoansController::class, 'WaitApproval']);
        Route::get('loan_capital', [CapitalController::class, 'show']);
        Route::get('collections', [CollectionController::class, 'index']);
        Route::get('loan-customer-details', [CustomerController::class, 'getCustomer']);
        Route::get('customer-progress', [CustomerController::class, 'getCustomersData']);
        Route::get('customer-with-loans', [CustomerController::class, 'customerWithLoans']);
        Route::get('customer-with-out-loans', [CustomerController::class, 'customerWithOutLoans']);
    });
});
