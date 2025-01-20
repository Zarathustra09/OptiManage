<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Employee\EmployeeItemController;
use App\Http\Controllers\Employee\EmployeeLogController;
use App\Http\Controllers\Employee\EmployeeTaskController;
use App\Http\Controllers\Employee\EmployeeTeamTaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamAssigneeController;
use App\Http\Controllers\TeamTaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/upload-image', [ProfileController::class, 'uploadProfileImage'])->name('profile.uploadImage');
Route::post('/profile/reset-image', [ProfileController::class, 'resetProfileImage'])->name('profile.resetImage');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//admin employee
Route::get('/admin/employee', [EmployeeController::class, 'index'])->name('admin.employee.index');
Route::get('/admin/employee/{id}', [EmployeeController::class, 'show'])->name('admin.employee.show');
Route::post('/admin/employee', [EmployeeController::class, 'store'])->name('admin.employee.store');
Route::put('/admin/employee/{id}', [EmployeeController::class, 'update'])->name('admin.employee.update');
Route::delete('/admin/employee/{id}', [EmployeeController::class, 'destroy'])->name('admin.employee.destroy');

//admin category
Route::get('/admin/category', [CategoryController::class, 'index'])->name('admin.category.index');
Route::get('/admin/category/{id}', [CategoryController::class, 'show'])->name('admin.category.show');
Route::post('/admin/category', [CategoryController::class, 'store'])->name('admin.category.store');
Route::put('/admin/category/{id}', [CategoryController::class, 'update'])->name('admin.category.update');
Route::delete('/admin/category/{id}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
Route::get('/admin/list', [CategoryController::class, 'list'])->name('admin.category.list');

//admin inventory
Route::get('/admin/inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');
Route::get('/admin/inventory/{id}', [InventoryController::class, 'show'])->name('admin.inventory.show');
Route::post('/admin/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
Route::put('/admin/inventory/{id}', [InventoryController::class, 'update'])->name('admin.inventory.update');
Route::delete('/admin/inventory/{id}', [InventoryController::class, 'destroy'])->name('admin.inventory.destroy');
Route::get('/admin/item/list', [InventoryController::class, 'allInventory'])->name('admin.inventory.list');

//admin tasks
Route::get('/admin/task', [TaskController::class, 'index'])->name('admin.task.index');
Route::get('/admin/task/create', [TaskController::class, 'create'])->name('admin.task.create');
Route::post('/admin/task', [TaskController::class, 'store'])->name('admin.task.store');
Route::get('/admin/task/{id}', [TaskController::class, 'show'])->name('admin.task.show');
Route::put('/admin/task/{id}', [TaskController::class, 'update'])->name('admin.task.update');
Route::delete('/admin/task/{id}', [TaskController::class, 'destroy'])->name('admin.task.destroy');


//admin availability
Route::get('availabilities/create/{userId}', [AvailabilityController::class, 'create'])->name('availabilities.create');
Route::get('availabilities/single/{id}', [AvailabilityController::class, 'showSingle'])->name('availabilities.showSingle');
Route::post('availabilities', [AvailabilityController::class, 'store'])->name('availabilities.store');
Route::put('availabilities/{id}', [AvailabilityController::class, 'update'])->name('availabilities.update');
Route::delete('availabilities/{id}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');
Route::get('availabilities/{id}', [AvailabilityController::class, 'show'])->name('availabilities.show');


//admin team task
Route::get('/admin/team-task', [TeamTaskController::class, 'index'])->name('admin.teamTask.index');
Route::get('/admin/team-task/create', [TeamTaskController::class, 'create'])->name('admin.teamTask.create');
Route::get('/admin/team-task/single/{id}', [TeamTaskController::class, 'showSingle'])->name('admin.teamTask.single');
Route::get('/admin/team-task/{id}', [TeamTaskController::class, 'show'])->name('admin.teamTask.show');
Route::post('/admin/team-task', [TeamTaskController::class, 'store'])->name('admin.teamTask.store');
Route::get('/admin/team-task/{id}/edit', [TeamTaskController::class, 'edit'])->name('admin.teamTask.edit');
Route::put('/admin/team-task/{id}', [TeamTaskController::class, 'update'])->name('admin.teamTask.update');
Route::delete('/admin/team-task/{id}', [TeamTaskController::class, 'destroy'])->name('admin.teamTask.destroy');


Route::post('/admin/team-task/add-inventory-item', [TeamTaskController::class, 'addInventoryItem'])->name('admin.teamTaskInventory.store');
Route::delete('/admin/team-task/remove-inventory-item/{id}', [TeamTaskController::class, 'removeInventoryItem'])->name('admin.teamTaskInventory.remove');


Route::get('/admin/team-assignee', [TeamAssigneeController::class, 'index'])->name('admin.teamAssignee.index');
Route::post('/admin/team-assignee', [TeamAssigneeController::class, 'store'])->name('admin.teamAssignee.store');
Route::get('/admin/team-assignee/{id}', [TeamAssigneeController::class, 'show'])->name('admin.teamAssignee.show');
Route::put('/admin/team-assignee/{id}', [TeamAssigneeController::class, 'update'])->name('admin.teamAssignee.update');
Route::delete('/admin/team-assignee/{id}', [TeamAssigneeController::class, 'destroy'])->name('admin.teamAssignee.destroy');

Route::get('/admin/taskCategory', [TaskCategoryController::class, 'index'])->name('admin.taskCategory.index');
Route::post('/admin/taskCategory', [TaskCategoryController::class, 'store'])->name('admin.taskCategory.store');
Route::get('/admin/taskCategory/{id}', [TaskCategoryController::class, 'show'])->name('admin.taskCategory.show');
Route::put('/admin/taskCategory/{id}', [TaskCategoryController::class, 'update'])->name('admin.taskCategory.update');
Route::delete('/admin/taskCategory/{id}', [TaskCategoryController::class, 'destroy'])->name('admin.taskCategory.destroy');

Route::get('/admin/logs', [LogController::class, 'index'])->name('admin.log.index');



Route::get('/employee/home', [\App\Http\Controllers\Employee\HomeController::class, 'index'])->name('employee.home');

//employee task

Route::get('/employee/task', [EmployeeTaskController::class, 'index'])->name('employee.task.index');
Route::get('/employee/task/create', [EmployeeTaskController::class, 'create'])->name('employee.task.create');
Route::post('/employee/task', [EmployeeTaskController::class, 'store'])->name('employee.task.store');
Route::get('/employee/task/{id}', [EmployeeTaskController::class, 'show'])->name('employee.task.show');
Route::delete('/employee/task/{id}', [EmployeeTaskController::class, 'destroy'])->name('employee.task.destroy');
Route::get('/employee/task/showSingle/{id}', [EmployeeTaskController::class, 'showSingle'])->name('employee.task.showSingle');


Route::get('/employee/team-task', [EmployeeTeamTaskController::class, 'index'])->name('employee.teamTask.index');
Route::get('/employee/team-task/{id}', [EmployeeTeamTaskController::class, 'show'])->name('employee.teamTask.show');
Route::put('/employee/team-task/{id}', [EmployeeTeamTaskController::class, 'update'])->name('employee.task.update');


Route::post('item/return-single/{teamTaskId}/{inventoryId}/{quantity}', [EmployeeItemController::class, 'returnSingle'])->name('employee.item.returnSingle');
Route::post('item/return-all/{teamTaskId}', [EmployeeItemController::class, 'returnAll'])->name('employee.item.returnAll');
Route::post('item/return-single-task-item/{taskId}/{inventoryId}/{quantity}', [EmployeeItemController::class, 'returnSingleTaskItem'])->name('employee.item.returnSingleTaskItem');
Route::post('item/return-all-task-items/{taskId}', [EmployeeItemController::class, 'returnAllTaskItems'])->name('employee.item.returnAllTaskItems');

Route::get('/employee/logs', [EmployeeLogController::class, 'index'])->name('employee.log.index');

