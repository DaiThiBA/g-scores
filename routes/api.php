<?php

use App\Http\Controllers\API\ExamResultController;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/exam-results/{sbd}', [ExamResultController::class, 'getScore']);
Route::get('/subject-reports/{subject}', [ExamResultController::class, 'getSubjectReport']);
Route::get('/top10-group-a', [ExamResultController::class, 'getTop10GroupA']);