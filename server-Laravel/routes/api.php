<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\CalendarController;

Route::group(["prefix" => "guest"], function(){
    Route::get("auth", [AuthController::class, "redirectToAuth"]);
    Route::get('auth/callback', [AuthController::class, 'handleAuthCallback']);
    Route::get("unauthorized", [AuthController::class, "unauthorized"])->name("unauthorized");
    Route::post("login", [AuthController::class, "login"]);
    Route::post("register", [AuthController::class, "register"]);
    Route::get('properties/{id?}', [GuestController::class, 'getProperties']);
    Route::get('trends', [GuestController::class, 'getTransactionsAndArticles']);
});

Route::group(["middleware" => "auth:api"], function(){
    
    Route::group(["middleware" => "auth.admin"], function(){
    });

    Route::group(["prefix" => "user"], function(){
        Route::post("logout", [AuthController::class, "logout"]);
        Route::post("refresh", [AuthController::class, "refresh"]);
        Route::get("profile", [AuthController::class, "profile"]);
        Route::post("updateUser", [AuthController::class, "updateUser"]);
        Route::post("addOrUpdate", [PropertyController::class, "AddOrUpdate"]);
        Route::get("userProperties", [PropertyController::class, "getUserProperties"]);
        Route::post("setAvailable", [CalendarController::class, "setAvailable"]);
        Route::post("bookMeeting", [CalendarController::class, "bookMeeting"]);
        Route::get("meetings", [CalendarController::class, "getMeetings"]);
    });

});


