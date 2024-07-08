<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\TrackingRecordController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

# ------ Unauthenticated routes ------ #
Route::get('/', [AuthenticatedSessionController::class, 'create']);
Route::get('/optimize', function () {
    Artisan::call('optimize:clear');
    return redirect('/scaninvoice');
});
Route::get('/migrate', function () {
    Artisan::call('migrate');
    return back();
});
Route::get('/seed-product-detail', function () {
    Artisan::call('db:seed --class=ProductDetailSeeder');
    return back();
});
Route::get('/scaninvoice', [RouteController::class, 'scan'])->name('scan.index');
Route::post('/scaninvoice', [RouteController::class, 'scanStore'])->name('scan.store');

Route::get('/scan', [RouteController::class, 'scanPengiriman'])->name('scan-pengiriman.index');
Route::post('/scan', [RouteController::class, 'scanPengirimanStore'])->name('scan-pengiriman.store');

require __DIR__ . '/auth.php';


# ------ Authenticated routes ------ #
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [RouteController::class, 'dashboard'])->name('home'); # dashboard

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'myProfile'])->name('profile');
        Route::put('/change-ava', [ProfileController::class, 'changeFotoProfile'])->name('change-ava');
        Route::put('/change-profile', [ProfileController::class, 'changeProfile'])->name('change-profile');
    }); # profile group

    Route::resource('users', UserController::class);
    Route::get('/tracking-records/reports', [TrackingRecordController::class, 'reports'])->name('tracking-records.reports');
    Route::resource('tracking-records', TrackingRecordController::class);
    Route::resource('products', ProductController::class);
    Route::get('/invoices/reports', [InvoiceController::class, 'reports'])->name('invoices.reports');
    Route::get('/invoices/{invoice_number}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::resource('invoices', InvoiceController::class);
});
