<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});
Route::get('/map', [MapController::class, 'index']);
Route::get('/api/properties', [MapController::class, 'getProperties']);