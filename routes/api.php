<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DataController;
use App\Http\Controllers\API\FileUploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('register', [AuthController::class, "register"]);
Route::post('login', [AuthController::class, "login"]);

// Route::get('/personas', [DataController::class, 'getPersonas']);

Route::middleware("auth:api")->group(function () {
    Route::get('/personas', [DataController::class, 'getPersonas']);

    Route::post('/Upload-data', [FileUploadController::class,'upload']);
});
