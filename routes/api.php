<?php


/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

*/

use App\Http\Controllers\Properties\PropertiesController;
use App\Http\Controllers\Properties\PropertiesRatingController;
use App\Http\Controllers\Properties\PropertiesRentController;
use App\Http\Controllers\Properties\PropertyImagesController;
use App\Http\Controllers\Users\UserAccountController;


Route::group(['prefix' => 'users'], function() {
    Route::post('/', [UserAccountController::class, 'store']);
    Route::post('email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');
    Route::patch('/{user}', [UserAccountController::class, 'update']);
    Route::delete('/{user}', [UserAccountController::class, 'destroy']);
});

Route::group(['prefix' => 'properties'], function() {
    Route::get('/', [PropertiesController::class, 'index']);
    Route::post('/', [PropertiesController::class, 'store']);
    /* images */
    Route::delete('/{property}/images/{image}', [PropertyImagesController::class, 'destroySingle']);
    Route::get('/{property}/images', [PropertyImagesController::class, 'index']);
    Route::post('/{property}/images', [PropertyImagesController::class, 'store']);
    Route::patch('/{property}/images', [PropertyImagesController::class, 'update']);
    Route::delete('/{property}/images', [PropertyImagesController::class, 'destroy']);
    /* renting */
    Route::post('/{property}/rent', [PropertiesRentController::class, 'store']);
    Route::patch('/{property}/rent', [PropertiesRentController::class, 'update']);
    Route::delete('/{property}/rent', [PropertiesRentController::class, 'destroy']);
    /* rating */
    Route::post('/{property}/rating', [PropertiesRatingController::class, 'store']);
    Route::delete('/{property}/rating/{rating}', [PropertiesRatingController::class, 'destroy']);

    Route::patch('/{property}', [PropertiesController::class, 'update']);
    Route::delete('/{property}', [PropertiesController::class, 'destroy']);
});



