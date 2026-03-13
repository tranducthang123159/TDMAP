<?php

// use App\Http\Controllers\ProfileController;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Admin\AdminController;
// use App\Http\Controllers\MapController;
// use App\Http\Controllers\Admin\MapAdminController;
// use Illuminate\Http\Request;
// Route::get('/', function () {
//     return view('index');
// });



// Route::get('/email/verification-status', function (Request $request) {

//     return response()->json([
//         'verified' => $request->user()->hasVerifiedEmail()
//     ]);

// })->middleware('auth');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route::middleware(['auth', 'role:admin'])
//     ->prefix('admin')
//     ->group(function () {

//         Route::get('/', function () {
//             return view('admin.giao_dien.index');
//         })->name('admin.dashboard');

//         Route::resource('users', UserController::class);
//         Route::get('/mapfiles', [MapAdminController::class, 'index'])->name('admin.mapfiles');

//         Route::get('/mapfiles/download/{id}', [MapAdminController::class, 'download'])->name('admin.mapfiles.download');
//     });

// Route::post('/upload-map', [MapController::class, 'upload'])->middleware('auth');
// require __DIR__ . '/auth.php';
// Route::get('/my-files', [MapController::class, 'myFiles'])->middleware('auth');
// Route::get('/download-map/{id}', [MapController::class, 'download'])
//     ->middleware('auth');





use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MapAdminController;

use App\Http\Controllers\Auth\OtpController;


/*
|--------------------------------------------------------------------------
| Trang chủ
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('index');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('index');
})->middleware(['auth','otp.active'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| OTP VERIFY
|--------------------------------------------------------------------------
*/

Route::get('/verify-otp', [OtpController::class,'form'])->name('otp.form');
Route::post('/verify-otp', [OtpController::class,'verify'])->name('otp.verify');

Route::get('/resend-otp',[OtpController::class,'resend'])
->middleware('auth')
->name('otp.resend');
/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','otp.active'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','otp.active','role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.giao_dien.index');
        })->name('admin.dashboard');

        Route::resource('users', UserController::class);

        Route::get('/mapfiles',[MapAdminController::class,'index'])
            ->name('admin.mapfiles');

        Route::get('/mapfiles/download/{id}',
            [MapAdminController::class,'download'])
            ->name('admin.mapfiles.download');

});


/*
|--------------------------------------------------------------------------
| Map
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','otp.active'])->group(function(){

Route::post('/upload-map',[MapController::class,'upload']);

Route::get('/my-files',[MapController::class,'myFiles']);

Route::get('/download-map/{id}',
    [MapController::class,'download']);

});


/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';