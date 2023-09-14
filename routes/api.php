<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PropertyController;

Route::group(["middleware" => "auth:api"], function(){
    
    Route::group(["middleware" => "auth.admin"], function(){
    });

    Route::group(["prefix" => "user"], function(){
        Route::post("logout", [AuthController::class, "logout"]);
        Route::post("refresh", [AuthController::class, "refresh"]);
        Route::post("addOrUpdate", [PropertyController::class, "AddOrUpdate"]);
        Route::get("userProperties", [PropertyController::class, "getUserProperties"]);
    });

});

Route::group(["prefix" => "guest"], function(){
    Route::get("unauthorized", [AuthController::class, "unauthorized"])->name("unauthorized");
    Route::post("login", [AuthController::class, "login"]);
    Route::post("register", [AuthController::class, "register"]);
    Route::get('/properties/{id?}', [GuestController::class, 'getProperties']);
    Route::get('/trends', [GuestController::class, 'getTransactionsAndArticles']);
});
