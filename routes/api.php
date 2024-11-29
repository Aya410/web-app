<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\NotificationController;
use Illuminate\Routing\Middleware\ThrottleRequests;
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




Route::middleware('check_user')->group(function () {
    Route::get('/showfilesforcheckout', [OperationController::class, 'showfilesforcheckout']);
    Route::post('/checkout', [OperationController::class, 'checkout']);
    Route::post('/checkin', [OperationController::class, 'checkin']);



    Route::get('/getMyGroups', [GroupController::class, 'getMyGroups']);


    Route::post('/getPendingGroupsForAuthUser', [GroupController::class, 'getPendingGroupsForAuthUser']);
    Route::post('/updateRequestJoin', [GroupController::class, 'updateRequestJoin']);

    Route::post('/getFileVersionsforuser', [GroupController::class, 'getFileVersions']);

    Route::post('/upload', [FileController::class, 'uploadFile']);

    Route::post('/getFileVersionsuser', [GroupController::class, 'getFileVersionsuser']);


Route::post('/store_group', [GroupController::class, 'store']);


    Route::post('/showAllUsers', [GroupController::class, 'showAllUsers']);


    Route::post('/getFilesByGroupId', [GroupController::class, 'getFilesByGroupId']);

    Route::post('/getHistory', [FileController::class, 'getHistory']);


});






Route::middleware('check_groupadmin')->group(function () {

    Route::post('/getFileVersionsforadmin', [GroupController::class, 'getFileVersions']);

    Route::get('/getGroupsforadmin', [GroupController::class, 'getGroups']);
 
    Route::post('/uploadFileadmin', [FileController::class, 'uploadFileadmin']);   

    Route::post('/search', [UserController::class, 'searchUser']);



    Route::get('/pending', [FileController::class, 'getPendingFiles']);

    Route::post('/response', [FileController::class, 'handleAdminResponse']);


    Route::post('/getUsersByGroupId', [UserController::class, 'getUsersByGroupId']);





Route::post('/getallFiles', [FileController::class, 'getallFiles']);





Route::post('/deleteFile', [FileController::class, 'deleteFile']);






});

Route::post('/send-notification', [NotificationController::class, 'sendTestNotification']);






