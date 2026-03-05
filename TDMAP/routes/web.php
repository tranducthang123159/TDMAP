<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
Route::get('/', function () {
    return view('index');
});



Route::get('/email/verification-status', function (Request $request) {

    return response()->json([
        'verified' => $request->user()->hasVerifiedEmail()
    ]);

})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.giao_dien.index');
        })->name('admin.dashboard');

     Route::resource('users', UserController::class);

    });
require __DIR__.'/auth.php';

