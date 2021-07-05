<?php


/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

*/

use App\Http\Controllers\Admins\AdminAccountController;
use App\Http\Controllers\Amenities\AmenitiesController;
use App\Http\Controllers\Facilities\FacilitiesController;
use App\Http\Controllers\Locations\StateController;
use App\Http\Controllers\Properties\PropertiesAmenitiesController;
use App\Http\Controllers\Properties\PropertiesController;
use App\Http\Controllers\Properties\PropertiesFacilitiesController;
use App\Http\Controllers\Properties\PropertiesRatingController;
use App\Http\Controllers\Properties\PropertiesRentController;
use App\Http\Controllers\Properties\PropertiesRulesController;
use App\Http\Controllers\Properties\PropertyImagesController;
use App\Http\Controllers\PropertyTypes\PropertyTypeController;
use App\Http\Controllers\Rules\RulesController;
use App\Http\Controllers\TypeOfPlace\TypeOfPlaceController;
use App\Http\Controllers\Users\UserAccountController;
use App\Http\Controllers\Users\UserLoginController;
use App\Http\Controllers\Users\UserPropertiesController;
use App\Http\Controllers\Users\UserRatingsController;
use App\Http\Controllers\Users\UserRentController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;


Route::post('login', [UserLoginController::class, 'login'])->name('user.login');
Route::post('logout', [UserLoginController::class, 'logout']);
Route::get('wilayas', [StateController::class, 'index'])->name('wilaya.index');

Route::post('email/verify/{id}', [VerificationController::class,'verify'])->name('verification.verify');
Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');

Route::group(['prefix' => 'users'], function() {
    Route::get('/', [UserAccountController::class, 'index'])->name('users.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('users.show');
    Route::post('/{id}/approve', [UserAccountController::class,"approve"])->name('user.approve');
    Route::post('/', [UserAccountController::class, 'store']);
    Route::patch('/{userId}', [UserAccountController::class, 'update']);
    Route::patch('/{userId}/password_reset', [UserAccountController::class, 'password_reset']);
    Route::delete('/{user}', [UserAccountController::class, 'destroy']);

    Route::get('/{userId}/properties', [UserPropertiesController::class, 'index'])->name('user.properties.index');
    Route::get('/{userId}/rent', [UserRentController::class, 'index']);
    /* rating users */
    Route::get('/{user}/rating', [UserRatingsController::class, 'index'])->name('user.rating.show');
    Route::post('/{user}/rating', [UserRatingsController::class, 'store'])->name('user.rating.store');
    Route::patch('/{renterId}/rating/{ratingId}', [UserRatingsController::class, 'update'])->name('user.rating.update');
    Route::delete('/{renterId}/rating/{ratingId}', [UserRatingsController::class, 'destroy'])->name('user.rating.destroy.single');

    // TODO
    /*
        Route::get('/{userId}/ratings', [UserRentController::class, 'index']);
        Route::get('/{userId}/ratings/properties', [UserRentController::class, 'index']);
    */
});

Route::group(['prefix' => 'admin'], function() {
    Route::post('login', [AdminAccountController::class,'login']);
    Route::post('logout', [AdminAccountController::class, 'logout']);
});

Route::group(['prefix' => 'properties'], function() {
    Route::get('/', [PropertiesController::class, 'index'])->name('property.index');
    Route::post('/', [PropertiesController::class, 'store'])->name('property.store');
    Route::get('/{id}', [PropertiesController::class, 'get'])->name('property.get');

    /* renting */

    Route::get('/{propertyId}/rent', [PropertiesRentController::class, 'index'])->name('property.rent.index');

    Route::post('/{propertyId}/rent/{rentId}/verify', [PropertiesRentController::class, 'verify'])->name('property.rent.verify');
    Route::post('/{propertyId}/rent/{rentId}/approve', [PropertiesRentController::class, 'approve'])->name('property.rent.approve');
    Route::post('/{propertyId}/rent/{rentId}/decline', [PropertiesRentController::class, 'decline'])->name('property.rent.decline');
    Route::get('/{propertyId}/rent/{rentId}', [PropertiesRentController::class, 'show'])->name('property.rent.show');
    Route::post('/{propertyId}/rent', [PropertiesRentController::class, 'store'])->name('property.rent.store');
    Route::delete('/{propertyId}/rent', [PropertiesRentController::class, 'destroy'])->name('property.rent.destroy');
    #Route::patch('/{property}/rent', [PropertiesRentController::class, 'update'])->name('property.rent.update');


    /* rating properties */
    Route::post('/{property}/rating', [PropertiesRatingController::class, 'store'])->name('property.rating.store');
    Route::patch('/{property}/rating/{ratingId}', [PropertiesRatingController::class, 'update'])->name('property.rating.update');
    Route::delete('/{property}/rating/{rating}', [PropertiesRatingController::class, 'destroy'])->name('property.rating.destroy.single');

    // TODO property_types

    /* property_rules */
    Route::get('/{propertyId}/rules', [PropertiesRulesController::class, 'index'])->name('property.rules.index');
    Route::post('/{propertyId}/rules', [PropertiesRulesController::class, 'store'])->name('property.rules.store');
    Route::delete('/{propertyId}/rules/{ruleId}', [PropertiesRulesController::class, 'destroy'])->name('property.rules.destroy');

    /* property_facilities */
    Route::get('/{propertyId}/facilities', [PropertiesFacilitiesController::class, 'index'])->name('property.facilities.index');
    Route::post('/{propertyId}/facilities', [PropertiesFacilitiesController::class, 'store'])->name('property.facilities.store');
    Route::delete('/{propertyId}/facilities/{facilityId}', [PropertiesFacilitiesController::class, 'destroy'])->name('property.facilities.destroy');

    /* property_amenities */
    Route::get('/{propertyId}/amenities', [PropertiesAmenitiesController::class, 'index'])->name('property.amenities.index');
    Route::post('/{propertyId}/amenities', [PropertiesAmenitiesController::class, 'store'])->name('property.amenities.store');
    Route::delete('/{propertyId}/amenities/{amenitiesId}', [PropertiesAmenitiesController::class, 'destroy'])->name('property.amenities.destroy');

    /* property_images */
    Route::delete('/{property}/images/{image}', [PropertyImagesController::class, 'destroySingle'])->name('property.images.destroy.single');
    Route::get('/{property}/images', [PropertyImagesController::class, 'index'])->name('property.images.index');
    Route::post('/{property}/images', [PropertyImagesController::class, 'store'])->name('property.images.store');
    Route::patch('/{property}/images', [PropertyImagesController::class, 'update'])->name('property.images.update');
    Route::delete('/{property}/images', [PropertyImagesController::class, 'destroy'])->name('property.images.destroy');

    Route::patch('/{property}', [PropertiesController::class, 'update'])->name('property.update');
    Route::delete('/{property}', [PropertiesController::class, 'destroy'])->name('property.destroy');
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

Route::group(['prefix' => 'type_of_place'], function() {
    Route::get('/',[TypeOfPlaceController::class,'index']);
    Route::post('/',[TypeOfPlaceController::class,'store']);
    Route::patch('/{typeOfPlace}',[TypeOfPlaceController::class,'update']);
    Route::delete('/{typeOfPlace}',[TypeOfPlaceController::class,'destroy']);
});
