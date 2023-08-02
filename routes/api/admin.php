<?php

use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CapitalController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\LoanTypeController;
use App\Http\Controllers\Admin\MessagesController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LastActivityController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'activity']], function () {
    Route::prefix('a')->group(function () {
        Route::apiResources([
            'messages' => MessagesController::class,
            'roles' => RoleController::class,
            'users' => UserController::class,
            'branches' => BranchController::class,
            'loan-types' => LoanTypeController::class,
            'customers' => CustomerController::class,
            'active-users' => LastActivityController::class,
            'capitals' => CapitalController::class,
            'reports' => ReportsController::class,
            'inventories' => AdminInventoryController::class,
        ]);

        Route::post('update-password', [AuthController::class, 'updatePassword']);

        Route::post('customer-status', [CustomerController::class, 'update']);
        Route::get('customer-details', [CustomerController::class, 'customerDetails']);
        Route::get('collections', [CollectionController::class, 'index']);
        Route::post('customer-with-loans', [CustomerController::class, 'customerWithLoans']);
        Route::post('customer-with-out-loans', [CustomerController::class, 'customerWithOutLoans']);
        Route::get('customer-progress', [CustomerController::class, 'getCustomersData']);
        Route::post('edit-capital', [CapitalController::class, 'update']);
        Route::get('my-account', [UserController::class, 'myAccount']);
        Route::get('loans-by-branch', [BranchController::class, 'loansByBranch']);
        Route::get('get-messages', [MessagesController::class, 'getMessages']);
        Route::get('daily-reports',[ReportsController::class, 'todaysReports']);
        Route::post('message-replies', [MessagesController::class, 'storeReplies']);
    });
});