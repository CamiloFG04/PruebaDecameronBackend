<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BedroomController;
use App\Http\Controllers\HotelController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('/login', [AuthController::class,'login'])->name('login');
    Route::post('/register', [AuthController::class,'register'])->name('register');
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class,'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class,'me'])->name('me');
});

Route::group(['middleware' => 'api','prefix' => 'hotels'], function () {
    // Hotels
    Route::get('/',[HotelController::class,'index'])->name('hotels');
    Route::post('/create',[HotelController::class,'store'])->name('hotels_create');
    Route::get('/{id}',[HotelController::class,'show'])->name('hotel');
    Route::put('/update/{id}',[HotelController::class,'update'])->name('hotel_update');
    Route::delete('/delete/{id}',[HotelController::class,'destroy'])->name('hotel_delete');

    // Rooms
    Route::post('/add_rooms/{id}',[BedroomController::class,'addRooms'])->name('hotel_add_rooms');
    Route::get('/hotel/{id}/rooms',[BedroomController::class,'showRooms'])->name('hotel_rooms');
});

