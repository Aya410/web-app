<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\Plat_AdminAuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([

    'prefix' => 'auth/user'

], function ($router) {
    Route::post('/register', [UserAuthController::class, 'register'])->name('register');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login');
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [UserAuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [UserAuthController::class, 'me'])->name('me');
    Route::post('/store_group', [GroupController::class, 'store']);

    Route::get('/getMyGroups', [GroupController::class, 'getMyGroups']);

    Route::post('/showAllUsers', [GroupController::class, 'showAllUsers']);

    
    Route::post('/store_file', [GroupController::class, 'store_file']);
    
    Route::post('/getFilesByGroupId', [GroupController::class, 'getFilesByGroupId']);

    Route::post('/getPendingGroupsForAuthUser', [GroupController::class, 'getPendingGroupsForAuthUser']);
    Route::post('/updateRequestJoin', [GroupController::class, 'updateRequestJoin']);
    
    
});


Route::group([

    'prefix' => 'auth/plat_admin'

], function ($router) {
    Route::post('/pregister', [Plat_AdminAuthController::class, 'register'])->name('pregister');
    Route::post('/plogin', [Plat_AdminAuthController::class, 'login'])->name('plogin');
    Route::post('/plogout', [Plat_AdminAuthController::class, 'logout'])->name('plogout');
    Route::post('/prefresh', [Plat_AdminAuthController::class, 'refresh'])->name('prefresh');
    Route::post('/pme', [Plat_AdminAuthController::class, 'me'])->name('pme');
});

