<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\AttendanceController;
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



Route::Post("/attendance", [AttendanceController::class, "addAttendance"]);
Route::Get("/attendance/{id}", [AttendanceController::class, "getAttendanceById"]);
Route::Get("/attendance", [AttendanceController::class, "getAllAttendance"]);
Route::Get("/attendance/section/{section_id}", [AttendanceController::class, "getAttendanceBySection"]);
Route::Get("/attendance/student/{student_id}", [AttendanceController::class, "getAttendanceByStudent"]);
Route::delete("/attendance/{id}", [AttendanceController::class, "deleteAttendanceById"]);
Route::Patch("/attendance/update/{id}", [AttendanceController::class, "updateAttendance"]);
#Route::Get("/attendance/bystudents/{id}", [AttendanceController::class, "getAttendanceByStudent"]);





