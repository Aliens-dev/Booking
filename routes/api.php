<?php


/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

*/

use App\Http\Controllers\Properties\PropertiesController;
use App\Http\Controllers\Properties\PropertiesRentController;
use App\Http\Controllers\Properties\PropertyImagesController;
use App\Http\Controllers\Users\UserAccountController;

Route::post('users', [UserAccountController::class, 'store']);
Route::post('users/email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
Route::post('users/email/resend', 'VerificationController@resend')->name('verification.resend');
Route::patch('/users/{user}', [UserAccountController::class, 'update']);
Route::delete('/users/{user}', [UserAccountController::class, 'destroy']);


Route::get('/properties', [PropertiesController::class, 'index']);
Route::post('/properties', [PropertiesController::class, 'store']);
Route::delete('/properties/{property}/images/{image}', [PropertyImagesController::class, 'destroySingle']);
Route::get('/properties/{property}/images', [PropertyImagesController::class, 'index']);
Route::post('/properties/{property}/images', [PropertyImagesController::class, 'store']);
Route::patch('/properties/{property}/images', [PropertyImagesController::class, 'update']);
Route::delete('/properties/{property}/images', [PropertyImagesController::class, 'destroy']);

Route::post('/properties/{property}/rent', [PropertiesRentController::class, 'store']);
Route::delete('/properties/{property}/rent', [PropertiesRentController::class, 'destroy']);

Route::patch('/properties/{property}', [PropertiesController::class, 'update']);
Route::delete('/properties/{property}', [PropertiesController::class, 'destroy']);

