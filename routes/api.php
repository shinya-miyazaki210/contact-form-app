<?php

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

Route::get('/v1/contacts', [\App\Http\Controllers\Api\V1\ContactController::class, 'index']);

Route::get('/v1/contacts/{contact}', [\App\Http\Controllers\Api\V1\ContactController::class, 'show']);

Route::post('/v1/contacts', [\App\Http\Controllers\Api\V1\ContactController::class, 'store']);

Route::put('/v1/contacts/{contact}', [\App\Http\Controllers\Api\V1\ContactController::class, 'update']);

Route::delete('/v1/contacts/{contact}', [\App\Http\Controllers\Api\V1\ContactController::class, 'destroy']);
