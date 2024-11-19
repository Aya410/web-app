<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});


//middleware name (check_user , check_groupadmin  , check_superadmin)





    Route::post('/store_group', [GroupController::class, 'store']);

    Route::get('/getMyGroups', [GroupController::class, 'getMyGroups']);

    Route::post('/showAllUsers', [GroupController::class, 'showAllUsers']);


    Route::post('/store_file', [GroupController::class, 'store_file']);

    Route::post('/getFilesByGroupId', [GroupController::class, 'getFilesByGroupId']);

    Route::post('/getPendingGroupsForAuthUser', [GroupController::class, 'getPendingGroupsForAuthUser']);
    Route::post('/updateRequestJoin', [GroupController::class, 'updateRequestJoin']);
    Route::post('/getFileVersions', [GroupController::class, 'getFileVersions']);







