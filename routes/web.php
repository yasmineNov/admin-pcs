<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\IncomingBarangController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\KasController;

/*
|--------------------------------------------------------------------------
| BASIC
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'));
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('banks', BankController::class);
    Route::resource('incoming-barangs', IncomingBarangController::class);
    Route::resource('mutasi-barangs', MutasiBarangController::class);
    Route::resource('orders', OrdersController::class);
    Route::resource('delivery-notes', DeliveryNoteController::class);
    Route::resource('payments', PaymentController::class)->only(['index', 'store', 'show', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| KAS
|--------------------------------------------------------------------------
*/

Route::prefix('kas')->name('kas.')->group(function () {
    Route::get('/', [KasController::class, 'index'])->name('index');
    Route::get('/create', [KasController::class, 'create'])->name('create');
    Route::post('/store', [KasController::class, 'store'])->name('store');
});

/*
|--------------------------------------------------------------------------
| PURCHASE ORDER
|--------------------------------------------------------------------------
*/

Route::prefix('pembelian/purchase-order')
    ->name('pembelian.purchase-order.')
    ->group(function () {
        Route::get('/', [OrdersController::class, 'indexPO'])->name('index');
        Route::get('/create', [OrdersController::class, 'createPO'])->name('create');
        Route::post('/store', [OrdersController::class, 'storePO'])->name('store');
    });

Route::get(
    '/pembelian/purchase-order/{id}/detail',
    [OrdersController::class, 'detail']
)
    ->name('pembelian.purchase-order.detail');

Route::get('/po/{id}', [OrdersController::class, 'showDetailPO']);
/*
|--------------------------------------------------------------------------
| SALES ORDER
|--------------------------------------------------------------------------
*/

Route::prefix('penjualan/sales-order')
    ->name('penjualan.sales-order.')
    ->group(function () {
        Route::get('/', [OrdersController::class, 'indexSO'])->name('index');
        Route::get('/create', [OrdersController::class, 'createSO'])->name('create');
        Route::post('/store', [OrdersController::class, 'storeSO'])->name('store');
    });

Route::get(
    '/penjualan/sales-order/{id}/detail',
    [OrdersController::class, 'detailSO']
)
    ->name('penjualan.sales-order.detail');
Route::get('/so/{id}', [OrdersController::class, 'showDetailSO']);
/*
|--------------------------------------------------------------------------
| DELIVERY NOTE PEMBELIAN
|--------------------------------------------------------------------------
*/

Route::prefix('pembelian/delivery-note')
    ->name('pembelian.delivery-note.')
    ->group(function () {
        Route::get('/', [DeliveryNoteController::class, 'indexMasuk'])->name('index');
        Route::get('/create', [DeliveryNoteController::class, 'createMasuk'])->name('create');
        Route::post('/', [DeliveryNoteController::class, 'store'])
            ->name('store')
            ->defaults('type', 'masuk');
        Route::get('/{deliveryNote}/edit', [DeliveryNoteController::class, 'edit'])->name('edit');
        Route::put('/{deliveryNote}', [DeliveryNoteController::class, 'update'])->name('update');
        Route::delete('/{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('destroy');
        Route::get('/{deliveryNote}', [DeliveryNoteController::class, 'show'])->name('show');
    });
Route::get('/dnpo/{id}', [DeliveryNoteController::class, 'showDetailPO']);


/*
|--------------------------------------------------------------------------
| DELIVERY NOTE PENJUALAN
|--------------------------------------------------------------------------
*/

Route::prefix('penjualan/delivery-note')
    ->name('penjualan.delivery-note.')
    ->group(function () {
        Route::get('/', [DeliveryNoteController::class, 'indexKeluar'])->name('index');
        Route::get('/create', [DeliveryNoteController::class, 'createKeluar'])->name('create');
        Route::post('/', [DeliveryNoteController::class, 'store'])
            ->name('store')
            ->defaults('type', 'keluar');
        Route::get('/{deliveryNote}/edit', [DeliveryNoteController::class, 'edit'])->name('edit');
        Route::put('/{deliveryNote}', [DeliveryNoteController::class, 'update'])->name('update');
        Route::delete('/{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('destroy');
        Route::get('/{deliveryNote}', [DeliveryNoteController::class, 'show'])->name('show');
    });
Route::get('/dnso/{id}', [DeliveryNoteController::class, 'showDetailSO']);


/*
|--------------------------------------------------------------------------
| INVOICE PEMBELIAN (MASUK)
|--------------------------------------------------------------------------
*/

Route::prefix('pembelian/invoice')
    ->name('pembelian.invoice.')
    ->group(function () {
        Route::get('/', [InvoiceController::class, 'indexMasuk'])->name('index');
        Route::get('/create', [InvoiceController::class, 'createMasuk'])->name('create');
        Route::post('/', [InvoiceController::class, 'storeMasuk'])->name('store');
    });

Route::get('/invPurchase/{id}/detail', [InvoiceController::class, 'detailPurchase']);
/*
|--------------------------------------------------------------------------
| INVOICE PENJUALAN (KELUAR)
|--------------------------------------------------------------------------
*/

Route::prefix('penjualan/invoice')
    ->name('penjualan.invoice.')
    ->group(function () {
        Route::get('/', [InvoiceController::class, 'indexKeluar'])->name('index');
        Route::get('/create', [InvoiceController::class, 'createKeluar'])->name('create');
        Route::post('/', [InvoiceController::class, 'storeKeluar'])->name('store');
    });

Route::get('/invSales/{id}/detail', [InvoiceController::class, 'detailSales']);

/*
|--------------------------------------------------------------------------
| DATA PEMBELIAN
|--------------------------------------------------------------------------
*/

Route::get('/pembelian/data-pembelian',[InvoiceController::class, 'dataPembelian'])->name('pembelian.data-pembelian.index');
Route::get('/pembelian/data-pembelian/export',[InvoiceController::class, 'exportPembelian'])->name('pembelian.data-pembelian.export');
Route::get('/pembelian/data-pembelian/print',[InvoiceController::class, 'printPembelian'])->name('pembelian.data-pembelian.print');

/*
|--------------------------------------------------------------------------
| DATA PEMBELIAN
|--------------------------------------------------------------------------
*/
Route::get('/penjualan/data-penjualan', [InvoiceController::class, 'dataPenjualan'])->name('penjualan.data-penjualan.index');
Route::get('/penjualan/data-penjualan/export',[InvoiceController::class, 'exportPenjualan'])->name('penjualan.data-penjualan.export');
Route::get('/penjualan/data-penjualan/print',[InvoiceController::class, 'printPenjualan'])->name('penjualan.data-penjualan.print');

/*
|--------------------------------------------------------------------------
| AJAX
|--------------------------------------------------------------------------
*/

Route::get(
    '/invoice/delivery-note/{id}',
    [InvoiceController::class, 'getDeliveryNoteDetail']
)
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






Route::get(
    '/pembelian/delivery-note/{id}/details',
    [InvoiceController::class, 'getDeliveryNoteDetail']
);

Route::get(
    '/penjualan/delivery-note/{id}/details',
    [InvoiceController::class, 'getDeliveryNoteDetail']
);

require __DIR__ . '/auth.php';
