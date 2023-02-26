<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\DetailController;



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

Route::Post("/students", [StudentController::class, "addStudent"]);
Route::Get("/students/{id}", [StudentController::class, "getStudentById"]);
Route::Get("/students", [StudentController::class, "getStudents"]);
Route::Get("/students/{section_id}", [StudentController::class, "getStudentsBySection"]);
Route::delete("/students/{id}", [StudentController::class, "deleteStudentById"]);
Route::Patch("/students/update/{id}", [StudentController::class, "updateStudent"]);
Route::Post('/sections',[SectionController::class,'addSection']);
Route::Get('/sections',[SectionController::class,'getAllSection']);
Route::Get('/sections/{id}',[SectionController::class,'getSection']);
Route::Patch('/sections/{id}',[SectionController::class,'editSection']);
Route::delete('/sections/{id}',[SectionController::class,'deleteSection']);
Route::Post('/admins',[AdminController::class,'addAdmin']);
Route::Get('/admins',[AdminController::class,'getAllAdmin']);
Route::Get('/admins/{id}',[AdminController::class,'getAdmin']);
Route::Patch('/admins/{id}',[AdminController::class,'editAdmin']);
Route::delete('/admins/{id}',[AdminController::class,'editAdmin']);
Route::Post('/classes',[ClassesController::class,'AddClass']);
Route::Get('/classes',[ClassesController::class,'GetClass']);
Route::Get('/classes/{id}',[ClassesController::class,'getClassById']);
Route::delete('/classes/{id}',[ClassesController::class,'deleteClass']);
Route::Patch('/classes/{id}',[ClassesController::class,'updateClass']);
Route::Get('/classes/name/{name}',[ClassesController::class,'getClassByName']);

Route::Post('/details',[DetailController::class,'AddDetail']);
Route::Get('/details',[DetailController::class,'GetDetail']);
Route::Get('/details/{id}',[DetailController::class,'getDetailById']);
Route::delete('/details/{id}',[DetailController::class,'deleteDetail']);
Route::Patch('/details/{id}',[DetailController::class,'editDetail']);
Route::Get('/details/type/{type}',[DetailController::class,'getDetailByType']);








