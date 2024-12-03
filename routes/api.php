<?php

use App\Http\Controllers\BankdetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DestinationController; 
use App\Http\Controllers\HotelController;
use App\Http\Controllers\SightseeingController;
use App\Http\Controllers\TransportationController;
use App\Http\Controllers\WalletController;

// Authenticated route to get the current user
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes for user registration and login
Route::post('/register', [UserController::class,'register']);
Route::get('/register/{id}', [UserController::class,'showdetailsuser']);
Route::post('/login', [UserController::class,'login']);


 
// Protected routes for destination management, requiring user authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/setdestination', [DestinationController::class, 'setDestination']);
    Route::get('/getdestination/{id}', [DestinationController::class, 'getDestination']);
    Route::post('/updatestatus', [DestinationController::class, 'updateStatus']);
    Route::get('/countries', [DestinationController::class, 'getCountries']);
    Route::get('/states/{country_id}', [DestinationController::class, 'getStates']);
    Route::get('/cities/{state_id}', [DestinationController::class, 'getCities']);


    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/changePassword',[UserController::class,'changePassword']);

// Protected routes for hotel management, requiring user authentication
    Route::post('/hotel',[HotelController::class,'hotel']);
    Route::get('/hotel/{id}',[HotelController::class,'show']);
    Route::post('/updatehotel/{id}',[HotelController::class,'updatehotel']);
    Route::delete('/deletehotel/{id}',[HotelController::class,'destroy']);

// Protected routes for sightseeing management, requiring user authentication
    Route::post('/sightseeing',[SightseeingController::class,'postSightseeing']);
    Route::get('/sightseeing/{id}',[SightseeingController::class,'showsightseeing']);
    Route::post('/updatesightseeing/{id}',[SightseeingController::class,'updatesightseeing']);
    Route::delete('/deletesightseeing/{id}',[SightseeingController::class,'destroy']);

// Protected routes for transportation management, requiring user authentication
    Route::post('/transportation',[TransportationController::class,'transportation']);
    Route::get('/showtransportation/{id}',[TransportationController::class,'showtransportation']);
    Route::post('/updatetransportation/{id}',[TransportationController::class,'updatetransportation']);
    Route::delete('/deletetransportation/{id}',[TransportationController::class,'destroy']);

// Protected routes for User banking info management, requiring user authentication
    Route::post('/bankdetail',[BankdetailController::class,'bankdetail']);
    Route::get('/bankdetail/{id}',[BankdetailController::class,'showbankdetail']);
    Route::post('/updatebankdetail/{id}',[BankdetailController::class,'updatebankdetail']);
    Route::post('/deletebankdetail/{id}',[BankdetailController::class,'delete']);

    Route::get('/showbalance/{userId}', [WalletController::class, 'showbalance']);
    Route::post('/wallet/update/{agentId}', [WalletController::class, 'updateWalletBalance']);

    



});