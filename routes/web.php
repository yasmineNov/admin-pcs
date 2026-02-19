<?php

use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\InvoiceController;
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

// Purchase Order
Route::prefix('pembelian/purchase-order')->name('pembelian.purchase-order.')->group(function() {
    Route::get('/', [OrdersController::class, 'indexPO'])->name('index');
    Route::get('/create', [OrdersController::class, 'createPO'])->name('create');
    Route::post('/store', [OrdersController::class, 'storePO'])->name('store');
});
//AJAX
Route::get('/pembelian/purchase-order/{id}/detail',[OrdersController::class, 'detail'])->name('pembelian.purchase-order.detail');

// Sales Order
Route::prefix('penjualan/sales-order')->name('penjualan.sales-order.')->group(function() {
    Route::get('/', [OrdersController::class, 'indexSO'])->name('index');
    Route::get('/create', [OrdersController::class, 'createSO'])->name('create');
    Route::post('/store', [OrdersController::class, 'storeSO'])->name('store');
});
//AJAX
Route::get('/penjualan/sales-order/{id}/detail',[OrdersController::class, 'detailSO'])->name('penjualan.sales-order.detail');

Route::prefix('pembelian')->group(function() {
    Route::get('delivery-note', [DeliveryNoteController::class, 'indexMasuk'])->name('pembelian.delivery-note.index');
    Route::get('delivery-note/create', [DeliveryNoteController::class, 'createMasuk'])->name('pembelian.delivery-note.create');
    Route::post('delivery-note/store', [DeliveryNoteController::class, 'store'])->name('pembelian.delivery-note.store')->defaults('type', 'masuk');
    Route::get('delivery-note/{deliveryNote}/edit', [DeliveryNoteController::class, 'edit'])->name('pembelian.delivery-note.edit');
    Route::put('delivery-note/{deliveryNote}', [DeliveryNoteController::class, 'update'])->name('pembelian.delivery-note.update');
    Route::delete('delivery-note/{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('pembelian.delivery-note.destroy');
    Route::get('pembelian/delivery-note/{deliveryNote}', [DeliveryNoteController::class, 'show']) ->name('pembelian.delivery-note.show');
});

Route::prefix('penjualan')->group(function() {
    Route::get('delivery-note', [DeliveryNoteController::class, 'indexKeluar'])->name('penjualan.delivery-note.index');
    Route::get('delivery-note/create', [DeliveryNoteController::class, 'createKeluar'])->name('penjualan.delivery-note.create');
    Route::post('delivery-note/store', [DeliveryNoteController::class, 'store'])->name('penjualan.delivery-note.store')->defaults('type', 'keluar');
    Route::get('delivery-note/{deliveryNote}/edit', [DeliveryNoteController::class, 'edit'])->name('penjualan.delivery-note.edit');
    Route::put('delivery-note/{deliveryNote}', [DeliveryNoteController::class, 'update'])->name('penjualan.delivery-note.update');
    Route::delete('delivery-note/{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('penjualan.delivery-note.destroy');
    Route::get('penjualan/delivery-note/{deliveryNote}', [DeliveryNoteController::class, 'show']) ->name('penjualan.delivery-note.show');
});

// Pembelian (Masuk)
// =====================
Route::get('/pembelian/invoice', [InvoiceController::class, 'indexMasuk'])->name('pembelian.invoice.index');
Route::get('/pembelian/invoice/create', [InvoiceController::class, 'createMasuk'])->name('pembelian.invoice.create');
Route::post('/pembelian/invoice', [InvoiceController::class, 'storeMasuk'])->name('pembelian.invoice.store'); // <-- POST route

// =====================
// Penjualan (Keluar)
// =====================
Route::get('/penjualan/invoice', [InvoiceController::class, 'indexKeluar'])->name('penjualan.invoice.index');
Route::get('/penjualan/invoice/create', [InvoiceController::class, 'createKeluar'])->name('penjualan.invoice.create');
Route::post('/penjualan/invoice', [InvoiceController::class, 'storeKeluar'])->name('penjualan.invoice.store'); // <-- POST route

Route::get('/invoice/delivery-note/{id}', [InvoiceController::class, 'getDeliveryNoteDetail'])
    ->name('invoice.getDeliveryNoteDetail');


    // PENJUALAN
// Route::prefix('penjualan')->name('penjualan.')->group(function () {
//     Route::get('invoice', [InvoiceController::class, 'index'])
//         ->defaults('type', 'penjualan')
//         ->name('invoice.index');

//     Route::get('invoice/create', [InvoiceController::class, 'create'])
//         ->defaults('type', 'penjualan')
//         ->name('invoice.create');
// });

// // PEMBELIAN
// Route::prefix('pembelian')->name('pembelian.')->group(function () {
//     Route::get('invoice', [InvoiceController::class, 'index'])
//         ->defaults('type', 'pembelian')
//         ->name('invoice.index');

//     Route::get('invoice/create', [InvoiceController::class, 'create'])
//         ->defaults('type', 'pembelian')
//         ->name('invoice.create');
        
// });

// Route::prefix('pembelian')->name('pembelian.')->group(function () {
//     Route::resource('invoice', InvoiceController::class);
// });

//modal ROUTEE

Route::get('/po/{id}', [OrdersController::class, 'show']);





require __DIR__.'/auth.php';
