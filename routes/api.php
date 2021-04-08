<?php


/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

*/


use App\Http\Controllers\Users\UserAccountController;


Route::post('/users', [UserAccountController::class, 'store']);
