<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\userController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('api/v1.0')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // Route::post('logout', [AuthController::class, 'logout']);
        Route::apiResource('users', userController::class);
    });

    Route::post('authenticated', [AuthController::class, 'index'])->middleware('auth:sanctum');
    $apiRoutes = base_path('routes/api');
    if (file_exists($apiRoutes)) {
        foreach (scandir($apiRoutes) as $file) {
            if ($file !== '.' && $file !== '..') {
                require $apiRoutes . '/' . $file;
            }
        }
    }
});
