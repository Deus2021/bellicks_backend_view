<?php

use App\Http\Controllers\Admin\CapitalController;
use App\Http\Controllers\BranchManager\CollectionController;
use App\Http\Controllers\Cashier\CashierCapitalController;
use App\Http\Controllers\Cashier\CashierCollectionController;
use App\Http\Controllers\Cashier\CashierCustomerController;
use App\Http\Controllers\Cashier\CashierIssuedLoanController;
use App\Http\Controllers\Cashier\CashierRepaymentController;
use App\Http\Controllers\Cashier\CashierRoleController;
use App\Http\Controllers\Cashier\ExpensesController;
use App\Http\Controllers\Cashier\PenaltiesController;
use App\Http\Controllers\Cashier\RepaymentController;
use App\Http\Controllers\Cashier\VistNewCustomersController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'activity']], function () {
    Route::prefix('c')->group(function () {
        Route::apiResources([
            'expenses' => ExpensesController::class,
            'roles' => CashierRoleController::class,
            'customers-details' => CashierRepaymentController::class,
            'customers-loans' => CashierIssuedLoanController::class,
            // 'repayment'=> RepaymentController::class
            'latest-loans' => CashierCollectionController::class,
            'fetch-customer' => CashierCustomerController::class,
            'capitals-issued' => CashierCapitalController::class,
            'penalties' => PenaltiesController::class,
            'vist-new-customer' => VistNewCustomersController::class
        ]);
        Route::post('repayment', [RepaymentController::class, 'repay']);
        Route::get('loan_capital', [CapitalController::class, 'show']);
        Route::get('collections', [CashierCollectionController::class, 'index']);
        Route::get('loan-customer-details', [CashierCustomerController::class, 'getCustomer']);
        Route::get('customer-progress', [CashierCustomerController::class, 'getCustomersData']);
        Route::get('customer-with-loans', [CashierCustomerController::class, 'customerWithLoans']);
        Route::get('customer-with-out-loans', [CashierCustomerController::class, 'customerWithOutLoans']);
        Route::post('register-customer', [CashierCustomerController::class, 'temporaryCustomers']);
        Route::post('loan-issued', [CashierIssuedLoanController::class, 'LoanIssued']);
        Route::post('view-customers', [VistNewCustomersController::class,'store']);
        Route::post('reports-customers', [CashierCustomerController::class,'reports']);
        Route::get('waitIssueDashboard' , [CashierIssuedLoanController ::class,'waitIssueCashier' ]);
        Route::get('daily-reports',[CashierReportsController::class, 'todaysReports']);
    });
});
