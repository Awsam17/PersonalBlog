<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'],function (){
    Route::post('/login',[AuthController::class,'login'])->name('login');
    Route::post('/register',[AuthController::class,'register'])->name('register');
    Route::post('/request_verify',[AuthController::class,'requestVerify'])->name('requestVerify');
    Route::post('/verify',[AuthController::class,'verify'])->name('verify');
});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:api')->name('logout');

Route::group(['prefix'=>'post','name'=>'post','middleware'=>'auth:api'],function(){
    Route::post('create',[PostController::class,'create'])->name('create');
    Route::get('show',[PostController::class,'show'])->name('show');
    Route::post('edit',[PostController::class,'edit'])->name('edit');
    Route::delete('delete',[PostController::class,'delete'])->name('delete');
});

