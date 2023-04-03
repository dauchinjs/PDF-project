<?php

use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/documents', [HomeController::class, 'getPDFs']);
Route::get('/documents/{document}', [HomeController::class, 'showPDF']);
Route::post('/documents/{title}', [HomeController::class, 'uploadPDF']);

Route::delete('/documents/{document}', [HomeController::class, 'deletePDF']);
