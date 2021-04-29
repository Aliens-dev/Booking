<?php


/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

*/


use App\Http\Controllers\Properties\PropertiesController;
use App\Http\Controllers\Users\UserAccountController;


Route::post('/users', [UserAccountController::class, 'store']);
Route::patch('/users/{user}', [UserAccountController::class, 'update']);
Route::delete('/users/{user}', [UserAccountController::class, 'destroy']);


Route::get('/properties', [PropertiesController::class, 'index']);
Route::post('/properties', [PropertiesController::class, 'store']);
Route::patch('/properties/{id}', [PropertiesController::class, 'update']);
