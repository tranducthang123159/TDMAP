<?php
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
})->middleware(['auth', 'otp.active'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| OTP VERIFY
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'otp.active'])->group(function () {
    Route::get('/verify-otp', [OtpController::class, 'form'])->name('otp.form');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
    Route::get('/resend-otp', [OtpController::class, 'resend'])->name('otp.resend');

    // 🔥 THÊM CÁI NÀY
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
});
/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'otp.active'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'otp.active', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

        Route::resource('users', UserController::class);

        Route::get('/mapfiles', [MapAdminController::class, 'index'])
            ->name('admin.mapfiles');

        Route::get('/mapfiles/download/{id}', [MapAdminController::class, 'download'])
            ->name('admin.mapfiles.download');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });

/*
|--------------------------------------------------------------------------
| Map
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Map
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'otp.active'])->group(function () {

    Route::post('/upload-map', [MapController::class, 'upload'])->name('map.upload');

    Route::get('/my-files', [MapController::class, 'myFiles'])->name('map.myfiles');

    Route::get('/my-map-files/json', [MapController::class, 'myFilesJson'])->name('map.myfiles.json');

    Route::get('/map-file/{id}/geojson', [MapController::class, 'getGeoJson'])->name('map.file.geojson');
    Route::get('/my-files-json', [MapController::class, 'myFilesJson'])->name('map.myfiles.json');

    Route::get('/download-map/{id}', [MapController::class, 'download'])->name('map.download');

});

use App\Http\Controllers\VipController;
use App\Http\Controllers\Admin\VipTransactionController;

Route::middleware('auth')->group(function () {
    Route::get('/vip/payment', [VipController::class, 'paymentPage'])->name('vip.payment');
    Route::post('/vip/payment/order', [VipController::class, 'createOrder'])->name('vip.payment.order');
    Route::get('/vip/payment/status/{transactionId}', [VipController::class, 'checkStatus'])->name('vip.payment.status');
    Route::post('/vip/payment/confirmed/{transactionId}', [VipController::class, 'userConfirmedPaid'])
    ->name('vip.payment.user-confirmed');
  
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/vip-transactions', [VipTransactionController::class, 'index'])->name('vip.transactions.index');
    Route::post('/vip-transactions/{transaction}/confirm', [VipTransactionController::class, 'confirm'])->name('vip.transactions.confirm');
    Route::post('/vip-transactions/{transaction}/cancel', [VipTransactionController::class, 'cancel'])->name('vip.transactions.cancel');
});
/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\VipUploadController;

Route::post('/upload-vip',[VipUploadController::class,'upload'])->middleware('auth');
require __DIR__ . '/auth.php';