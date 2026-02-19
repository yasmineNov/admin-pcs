<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Orders;
use App\Models\Invoice;
use App\Models\OrderDetail;
use App\Models\Supplier;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    // ===========================
    // INDEX
    // ===========================

    public function indexMasuk()
    {
        $invoices = Invoice::with(['supplier', 'deliveryNote.order'])
            ->where('type', 'in') // sesuai enum DB
            ->latest()
            ->get();

        return view('pembelian.invoice.index', compact('invoices'));
    }

    public function indexKeluar()
    {
        $invoices = Invoice::with('details.orderDetail.barang', 'deliveryNote.order.customer')
            ->where('type', 'out') // sesuai enum DB
            ->latest()
            ->get();

        return view('penjualan.invoice.index', compact('invoices'));
    }

    // ===========================
    // DATA PEMBELIAN
    // ===========================

public function dataPembelian(Request $request)
{
    $query = Invoice::with([
        'supplier',
        'details.orderDetail'
    ])->where('type', Invoice::TYPE_MASUK);

        // Filter tanggal
        if ($request->from && $request->to) {
            $query->whereBetween('tgl', [
                Carbon::parse($request->from),
                Carbon::parse($request->to)
            ]);
        }

        $invoices = $query->latest()->get();

        // Summary
        $totalDpp = $invoices->sum('dpp');
        $totalPpn = $invoices->sum('ppn');
        $grandTotal = $invoices->sum('grand_total');
        $suppliers = \App\Models\Supplier::all(); // <<< WAJIB ADA INI

        return view('pembelian.data-pembelian.index', compact(
            'invoices',
            'totalDpp',
            'totalPpn',
            'grandTotal',
            'suppliers'
        ));
    }



 // ===========================
    // DATA PENJUALAN
    // ===========================

    public function dataPenjualan(Request $request)
{
    $query = Invoice::with([
        'customer',
        'details.orderDetail'
    ])->where('type', Invoice::TYPE_KELUAR);

    if ($request->filled('from') && $request->filled('to')) {
        $query->whereBetween('tgl', [$request->from, $request->to]);
    }

    if ($request->filled('customer_id')) {
        $query->where('customer_id', $request->customer_id);
    }

    $invoices = $query->latest()->get();

    $totalDpp = $invoices->sum('dpp');
    $totalPpn = $invoices->sum('ppn');
    $grandTotal = $invoices->sum('grand_total');

    $customers = \App\Models\Customer::all();

    return view('penjualan.data-penjualan.index', compact(
        'invoices',
        'totalDpp',
        'totalPpn',
        'grandTotal',
        'customers'
    ));
}
public function exportPenjualan(Request $request)
{
    $query = Invoice::with('customer')
        ->where('type', Invoice::TYPE_KELUAR);

    if ($request->filled('from') && $request->filled('to')) {
        $query->whereBetween('tgl', [$request->from, $request->to]);
    }

    if ($request->filled('customer_id')) {
        $query->where('customer_id', $request->customer_id);
    }

    $invoices = $query->get();

    $filename = "laporan_penjualan.xls";

    $headers = [
        "Content-Type" => "application/vnd.ms-excel",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    return response()->view(
        'penjualan.data-penjualan.excel',
        compact('invoices'),
        200,
        $headers
    );
}
public function printPenjualan(Request $request)
{
    $query = Invoice::with('customer')
        ->where('type', Invoice::TYPE_KELUAR);

    if ($request->filled('from') && $request->filled('to')) {
        $query->whereBetween('tgl', [$request->from, $request->to]);
    }

    if ($request->filled('customer_id')) {
        $query->where('customer_id', $request->customer_id);
    }

    $invoices = $query->latest()->get();

    $totalDpp = $invoices->sum('dpp');
    $totalPpn = $invoices->sum('ppn');
    $grandTotal = $invoices->sum('grand_total');

    return view('penjualan.data-penjualan.print', compact(
        'invoices',
        'totalDpp',
        'totalPpn',
        'grandTotal'
    ));
}

    // ===========================
    // LAPORAN HUTANG
    // ===========================

public function laporanHutang(Request $request)
{
    $suppliers = Supplier::with([
        'invoices' => function ($q) {
            $q->where('type', Invoice::TYPE_MASUK);
        },
        'invoices.paymentDetails'
    ])->get();

    return view('pembelian.hutang.index', compact('suppliers'));
}
public function getHutangDetail($supplierId)
{
    $invoices = Invoice::with('paymentDetails')
        ->where('supplier_id', $supplierId)
        ->where('type', Invoice::TYPE_MASUK)
        ->get();

    $data = $invoices->map(function($inv){

        $paid = $inv->paymentDetails->sum('subtotal');
        $sisa = $inv->grand_total - $paid;

        if($sisa <= 0) return null;

        return [
            'tgl' => $inv->tgl->format('d-m-Y'),
            'no' => $inv->no,
            'no_so' => $inv->no_so,
            'jatuh_tempo' => $inv->jatuh_tempo->format('d-m-Y'),
            'total' => number_format($inv->grand_total,0,',','.'),
            'paid' => number_format($paid,0,',','.'),
            'sisa' => number_format($sisa,0,',','.')
        ];
    })->filter()->values();

    return response()->json($data);
}

public function bayarHutang(Request $request)
{
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'jumlah_bayar' => 'required|numeric|min:1',
        'metode' => 'required'
    ]);

    DB::beginTransaction();

    try {

        $supplierId = $request->supplier_id;
        $sisaPembayaran = $request->jumlah_bayar;

        // Ambil semua invoice hutang supplier (urut paling lama)
        $invoices = Invoice::where('supplier_id', $supplierId)
            ->where('type', Invoice::TYPE_MASUK)
            ->orderBy('tgl', 'asc')
            ->get();

        // Buat header payment
        $payment = Payment::create([
            'total' => $request->jumlah_bayar,
            'keterangan' => 'Pelunasan Hutang',
            'type' => 'hutang',
            'supplier_id' => $supplierId,
            'customer_id' => null,
        ]);

        foreach ($invoices as $invoice) {

            if ($sisaPembayaran <= 0) break;

            $sudahDibayar = $invoice->paymentDetails()->sum('subtotal');
            $kurang = $invoice->grand_total - $sudahDibayar;

            if ($kurang <= 0) continue;

            if ($sisaPembayaran >= $kurang) {
                // lunas
                $bayar = $kurang;
            } else {
                // bayar sebagian
                $bayar = $sisaPembayaran;
            }

            // simpan detail pembayaran
            PaymentDetail::create([
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'subtotal' => $bayar
            ]);

            // update kolom paid di invoice
            $invoice->paid += $bayar;

            if ($invoice->paid >= $invoice->grand_total) {
                $invoice->status = 'paid';
            }

            $invoice->save();

            $sisaPembayaran -= $bayar;
        }

        DB::commit();

        return back()->with('success', 'Pembayaran berhasil disimpan.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->withErrors($e->getMessage());
    }
}


    // ===========================
    // CREATE
    // ===========================

    public function createMasuk()
    {
        $suppliers = \App\Models\Supplier::all();
        $deliveryNotes = DeliveryNote::where('type', 'masuk')->with('order.supplier')->get();
        $orderDetails = OrderDetail::with('barang')->get();

        return view('pembelian.invoice.create', compact('suppliers', 'deliveryNotes', 'orderDetails'));
    }

    public function createKeluar()
    {
        $customers = \App\Models\Customer::all();
        $deliveryNotes = DeliveryNote::where('type', 'keluar')->with('order.customer')->get();
        $orderDetails = OrderDetail::with('barang')->get();

        return view('penjualan.invoice.create', compact('customers', 'deliveryNotes', 'orderDetails'));
    }

    // ===========================
    // STORE
    // ===========================

    public function storeMasuk(Request $request)
    {
        $request->validate([
            'no' => 'required|unique:invoices,no',
            'tgl' => 'required|date',
            'jatuh_tempo' => 'required|date',
            'delivery_note_id' => 'required|exists:delivery_notes,id',
            'details.*.order_detail_id' => 'required|exists:order_details,id',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        $dn = DeliveryNote::with('order.supplier')->findOrFail($request->delivery_note_id);
        $supplier_id = $dn->order->supplier->id ?? null;
        $no_so = $dn->order->no ?? null;

        $dpp = collect($request->details)->sum(fn($item) => $item['qty'] * $item['harga']);
        $ppn = $dpp * 0.11;
        $total = $dpp + $ppn;

        $invoice = Invoice::create([
            'no' => $request->no,
            'no_so' => $no_so,
            'tgl' => $request->tgl,
            'jatuh_tempo' => $request->jatuh_tempo,
            'delivery_note_id' => $dn->id,
            'customer_id' => null,
            'supplier_id' => $supplier_id,
            'dpp' => $dpp,
            'ppn' => $ppn,
            'grand_total' => $total,
            'status' => 'unpaid',
            'paid' => 0,
            'type' => Invoice::TYPE_MASUK,
        ]);

        foreach ($request->details as $item) {
            $invoice->details()->create([
                'order_detail_id' => $item['order_detail_id'],
                'subtotal' => $item['qty'] * $item['harga'],
            ]);
        }

        return redirect()->route('pembelian.invoice.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    public function storeKeluar(Request $request)
    {
        $request->validate([
            'tgl' => 'required|date',
            'jatuh_tempo' => 'required|date',
            'delivery_note_id' => 'required|exists:delivery_notes,id',
            'details' => 'required|array|min:1',
            'details.*.order_detail_id' => 'required|exists:order_details,id',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            // Ambil delivery note + customer
            $dn = DeliveryNote::with('order.customer')
                ->findOrFail($request->delivery_note_id);

            $customer_id = $dn->order->customer->id ?? null;
            $no_so = $dn->order->no ?? null;

            if (!$customer_id) {
                throw new \Exception('Customer tidak ditemukan pada Delivery Note.');
            }

            // Hitung DPP dari detail
            $dpp = collect($request->details)->sum(function ($item) {
                return $item['qty'] * $item['harga'];
            });

            $ppn = $dpp * 0.11;
            $grand_total = $dpp + $ppn;

            // Generate nomor invoice di sini (lebih aman)
            $invoiceNumber = generateDocumentNumber('invoices', 'INV');

            // Simpan invoice
            $invoice = Invoice::create([
                'no' => $invoiceNumber,
                'no_so' => $no_so,
                'tgl' => $request->tgl,
                'jatuh_tempo' => $request->jatuh_tempo,
                'delivery_note_id' => $dn->id,
                'customer_id' => $customer_id,
                'supplier_id' => null,
                'dpp' => $dpp,
                'ppn' => $ppn,
                'grand_total' => $grand_total,
                'status' => 'unpaid',
                'paid' => 0,
                'type' => Invoice::TYPE_KELUAR,
            ]);

            // Simpan detail
            foreach ($request->details as $item) {

                $invoice->details()->create([
                    'order_detail_id' => $item['order_detail_id'],
                    'subtotal' => $item['qty'] * $item['harga'],
                ]);

            }

            DB::commit();

            return redirect()
                ->route('penjualan.invoice.index')
                ->with('success', 'Invoice berhasil dibuat.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }


    // ===========================
    // EDIT
    // ===========================

    public function edit(Invoice $invoice)
    {
        if ($invoice->type == 'in') {
            $orders = Orders::where('type', 'purchase')
                ->where('status', '!=', 'draft')
                ->with('details.barang', 'supplier')
                ->get();
            $invoice->load('details.orderDetail.barang');

            return view('pembelian.invoice.edit', compact('invoice', 'orders'));
        } else {
            $orders = Orders::where('type', 'sales')
                ->where('status', '!=', 'draft')
                ->with('details.barang', 'customer')
                ->get();
            $invoice->load('details.orderDetail.barang');

            return view('penjualan.invoice.edit', compact('invoice', 'orders'));
        }
    }

    // ===========================
    // SHOW
    // ===========================

    public function show(Invoice $invoice)
    {
        $invoice->load('details.orderDetail.barang', 'deliveryNote.order');

        if ($invoice->type == 'in') {
            return view('pembelian.invoice.show', compact('invoice'));
        } else {
            return view('penjualan.invoice.show', compact('invoice'));
        }
    }

    // ===========================
    // UPDATE
    // ===========================

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'tgl' => 'required|date',
            'details.*.order_detail_id' => 'required|exists:order_details,id',
        ]);

        foreach ($invoice->details as $d) {
            $barang = $d->orderDetail->barang;
            $qty = $d->orderDetail->qty;
            if ($invoice->type == 'in') {
                $barang->stok -= $qty;
            } else {
                $barang->stok += $qty;
            }
            $barang->save();
        }

        $invoice->update([
            'tgl' => $request->tgl,
            'keterangan' => $request->keterangan,
            'alamat_kirim' => $request->alamat_kirim
        ]);

        $invoice->details()->delete();

        foreach ($request->details as $item) {
            $detail = $invoice->details()->create([
                'order_detail_id' => $item['order_detail_id'],
                'keterangan' => $item['keterangan'] ?? null
            ]);

            $barang = $detail->orderDetail->barang;
            $qty = $detail->orderDetail->qty;
            if ($invoice->type == 'in') {
                $barang->stok += $qty;
            } else {
                $barang->stok -= $qty;
            }
            $barang->save();
        }

        $route = $invoice->type == 'in' ? 'pembelian.invoice.index' : 'penjualan.invoice.index';
        return redirect()->route($route)->with('success', 'Invoice berhasil diupdate.');
    }

    // ===========================
    // DELETE
    // ===========================

    public function destroy(Invoice $invoice)
    {
        foreach ($invoice->details as $d) {
            $barang = $d->orderDetail->barang;
            $qty = $d->orderDetail->qty;
            if ($invoice->type == 'in') {
                $barang->stok -= $qty;
            } else {
                $barang->stok += $qty;
            }
            $barang->save();
        }

        $invoice->delete();

        $route = $invoice->type == 'in' ? 'pembelian.invoice.index' : 'penjualan.invoice.index';
        return redirect()->route($route)->with('success', 'Invoice berhasil dihapus.');
    }

    // ===========================
    // GET ORDER DETAILS
    // ===========================

    public function getOrderDetail($id)
    {
        $order = Orders::with('details.barang')->findOrFail($id);

        $items = $order->details->map(function ($detail) {
            return [
                'barang_id' => $detail->barang->id,
                'nama_barang' => $detail->barang->nama_barang,
                'qty' => $detail->qty,
            ];
        });

        return response()->json($items);
    }

    public function detailSales($id)
    {
        $invoice = Invoice::with([
            'deliveryNote.details.orderDetail.barang',
            'deliveryNote.order.customer'
        ])->findOrFail($id);

        return view('penjualan.invoice.detail', compact('invoice'));
    }
    public function detailPurchase($id)
    {
        $invoice = Invoice::with([
            'deliveryNote.details.orderDetail.barang',
            'deliveryNote.order.customer'
        ])->findOrFail($id);

        return view('pembelian.invoice.detail', compact('invoice'));
    }




    public function getDeliveryNoteDetail($id)
    {
        $dn = DeliveryNote::with('details.orderDetail.barang', 'order.customer', 'order.supplier')->findOrFail($id);

        $items = $dn->details->map(fn($d) => [
            'barang_id' => $d->orderDetail->barang->id,
            'order_detail_id' => $d->orderDetail->id,
            'nama_barang' => $d->orderDetail->barang->nama_barang,
            'qty' => $d->OrderDetail->qty, // pakai qty yang dikirim
            'harga' => $d->orderDetail->harga, // INI WAJIB
            'supplier_name' => $dn->order->supplier->nama_supplier ?? '',
            'customer_name' => $dn->order->customer->nama_customer ?? '',
        ]);


        return response()->json($items);
    }
}
