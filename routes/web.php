<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('requestDoc', \App\Http\Controllers\DocumentRequestController::class);
