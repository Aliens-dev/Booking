<?php


/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

*/

use App\Http\Controllers\Amenities\AmenitiesController;
use App\Http\Controllers\Facilities\FacilitiesController;
use App\Http\Controllers\Properties\PropertiesAmenitiesController;
use App\Http\Controllers\Properties\PropertiesController;
use App\Http\Controllers\Properties\PropertiesFacilitiesController;
use App\Http\Controllers\Properties\PropertiesRatingController;
use App\Http\Controllers\Properties\PropertiesRentController;
use App\Http\Controllers\Properties\PropertiesRulesController;
use App\Http\Controllers\Properties\PropertyImagesController;
use App\Http\Controllers\PropertyTypes\PropertyTypeController;
use App\Http\Controllers\Rules\RulesController;
use App\Http\Controllers\Users\UserAccountController;
use App\Http\Controllers\Users\UserLoginController;
use Illuminate\Support\Facades\Route;


Route::post('login', [UserLoginController::class, 'login']);
Route::post('logout', [UserLoginController::class, 'logout']);

Route::group(['prefix' => 'users'], function() {
    Route::post('/', [UserAccountController::class, 'store']);
    Route::post('email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');
    Route::patch('/{user}', [UserAccountController::class, 'update']);
    Route::delete('/{user}', [UserAccountController::class, 'destroy']);
});

Route::group(['prefix' => 'rules'], function() {
    Route::get('/',[RulesController::class,'index']);
    Route::post('/',[RulesController::class,'store']);
    Route::patch('/{rule}',[RulesController::class,'update']);
    Route::delete('/{rule}',[RulesController::class,'destroy']);
});
Route::group(['prefix' => 'facilities'], function() {
    Route::get('/',[FacilitiesController::class,'index']);
    Route::post('/',[FacilitiesController::class,'store']);
    Route::patch('/{facility}',[FacilitiesController::class,'update']);
    Route::delete('/{facility}',[FacilitiesController::class,'destroy']);
});
Route::group(['prefix' => 'amenities'], function() {
    Route::get('/',[AmenitiesController::class,'index']);
    Route::post('/',[AmenitiesController::class,'store']);
    Route::patch('/{amenity}',[AmenitiesController::class,'update']);
    Route::delete('/{amenity}',[AmenitiesController::class,'destroy']);
});

Route::group(['prefix' => 'property_types'], function() {
    Route::get('/',[PropertyTypeController::class,'index']);
    Route::post('/',[PropertyTypeController::class,'store']);
    Route::patch('/{propertyType}',[PropertyTypeController::class,'update']);
    Route::delete('/{propertyType}',[PropertyTypeController::class,'destroy']);
});

Route::group(['prefix' => 'properties'], function() {
    Route::get('/', [PropertiesController::class, 'index']);
    Route::post('/', [PropertiesController::class, 'store']);
    Route::get('/{id}', [PropertiesController::class, 'get']);

    /* images */
    Route::delete('/{property}/images/{image}', [PropertyImagesController::class, 'destroySingle']);
    Route::get('/{property}/images', [PropertyImagesController::class, 'index']);
    Route::post('/{property}/images', [PropertyImagesController::class, 'store']);
    Route::patch('/{property}/images', [PropertyImagesController::class, 'update']);
    Route::delete('/{property}/images', [PropertyImagesController::class, 'destroy']);

    /* renting */
    Route::get('/{property}/rent', [PropertiesRentController::class, 'index']);
    Route::post('/{property}/rent', [PropertiesRentController::class, 'store']);
    Route::patch('/{property}/rent', [PropertiesRentController::class, 'update']);
    Route::delete('/{property}/rent', [PropertiesRentController::class, 'destroy']);

    /* rating */
    Route::post('/{property}/rating', [PropertiesRatingController::class, 'store']);
    Route::delete('/{property}/rating/{rating}', [PropertiesRatingController::class, 'destroy']);

    /* property_rules */
    Route::get('/{propertyId}/rules', [PropertiesRulesController::class, 'index']);
    Route::post('/{propertyId}/rules', [PropertiesRulesController::class, 'store']);
    Route::delete('/{propertyId}/rules/{ruleId}', [PropertiesRulesController::class, 'destroy']);
    /* property_facilities */
    Route::get('/{propertyId}/facilities', [PropertiesFacilitiesController::class, 'index']);
    Route::post('/{propertyId}/facilities', [PropertiesFacilitiesController::class, 'store']);
    Route::delete('/{propertyId}/facilities/{facilityId}', [PropertiesFacilitiesController::class, 'destroy']);
    /* property_amenities */
    Route::get('/{propertyId}/amenities', [PropertiesAmenitiesController::class, 'index']);
    Route::post('/{propertyId}/amenities', [PropertiesAmenitiesController::class, 'store']);
    Route::delete('/{propertyId}/amenities/{amenitiesId}', [PropertiesAmenitiesController::class, 'destroy']);

    Route::patch('/{property}', [PropertiesController::class, 'update']);
    Route::delete('/{property}', [PropertiesController::class, 'destroy']);
});



