<?php

use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\IncomingBarangController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\KasController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class);
});

Route::resource('barang', BarangController::class)
    ->middleware('auth')
    ->names('barang');

Route::resource('barangs', BarangController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('customers', CustomerController::class);
Route::resource('banks', BankController::class);

Route::resource('incoming-barangs', IncomingBarangController::class);
Route::resource('mutasi-barangs', MutasiBarangController::class);
Route::resource('orders', OrdersController::class);
Route::resource('delivery-notes', DeliveryNoteController::class);
Route::resource('invoices', InvoiceController::class);
Route::resource('payments', PaymentController::class)->only(['index','store','show','destroy']);




Route::prefix('kas')->group(function () {
    Route::get('/', [KasController::class, 'index'])->name('kas.index');
    Route::get('/create', [KasController::class, 'create'])->name('kas.create');
    Route::post('/store', [KasController::class, 'store'])->name('kas.store');
});







require __DIR__.'/auth.php';
