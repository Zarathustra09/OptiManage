<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\Employee\EmployeeTaskController;
use App\Http\Controllers\Employee\ImageController;
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


Route::post('/post/image/{taskId}', [ImageController::class, 'storeTaskImage'])->name('image.store');
Route::delete('/delete/image/{id}', [ImageController::class, 'destroyImage'])->name('image.destroy');
Route::post('/post/team-task-image/{teamTaskId}', [ImageController::class, 'storeTeamTaskImage'])->name('teamTaskImage.store');
Route::delete('/delete/team-task-image/{id}', [ImageController::class, 'destroyTeamTaskImage'])->name('teamTaskImage.destroy');



Route::get('/free/employee', [AvailabilityController::class, 'getAvailableUsers'])->name('available.employee.index');
