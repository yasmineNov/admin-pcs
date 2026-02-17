<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use App\Models\MutasiBarang;
use App\Models\Supplier;
use App\Models\Orders;
use App\Models\Customer;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    // View: pembelian/surat-jalan/index
    public function indexMasuk(Request $request)
    {
       $query = DeliveryNote::with('supplier')
        ->where('type', 'masuk');

    if ($request->search) {
        $search = '%' . $request->search . '%';

        $query->where(function ($q) use ($search) {
            $q->where('no', 'like', $search)
              ->orWhereHas('supplier', function ($sub) use ($search) {
                  $sub->where('nama', 'like', $search);
              });
        });
    }

    $data = $query->latest()->get();

    return view('pembelian.surat-jalan.index', compact('data'));
    }

    // View: penjualan/surat-jalan/index
    public function indexKeluar(Request $request)
{
    $query = DeliveryNote::with('customer')
        ->where('type', 'keluar');

    if ($request->search) {
        $search = '%' . $request->search . '%';

        $query->where(function ($q) use ($search) {
            $q->where('no', 'like', $search)
              ->orWhereHas('customer', function ($sub) use ($search) {
                  $sub->where('nama', 'like', $search);
              });
        });
    }

    $data = $query->latest()->get();

    return view('penjualan.surat-jalan.index', compact('data'));
}


    // View: pembelian/surat-jalan/create
    public function createMasuk()
    {
        $suppliers = Supplier::all();
        $barangs = Barang::all();

        return view('pembelian.surat-jalan.create',
            compact('suppliers','barangs'));
    }

    // View: penjualan/surat-jalan/create
    public function createKeluar()
    {
        $customers = Customer::all();
        $barangs = Barang::all();

        return view('penjualan.surat-jalan.create',
            compact('customers','barangs'));
    }

    public function create()
    {
        $customers = Customer::all();
        $barangs = Barang::all();
        $orders = Orders::with('customer','details.barang')
                    ->where('status','approved')
                    ->get();

        return view('penjualan.surat-jalan.create', compact('customers', 'orders','barangs'));
    }

public function storeMasuk(Request $request)
{
    $request->validate([
        'tgl' => 'required|date',
        'barang_id' => 'required|array',
        'qty' => 'required|array',
    ]);

    DB::beginTransaction();

    try {

        $sj = DeliveryNote::create([
            'no' => 'SJ-M-' . now()->format('YmdHis'),
            'tgl' => $request->tgl,
            'type' => 'masuk',
        ]);

        foreach ($request->barang_id as $key => $barangId) {

            $qty = $request->qty[$key];
            $barang = Barang::findOrFail($barangId);

            // tambah stok
            $barang->stok += $qty;
            $barang->save();

            DeliveryNoteDetail::create([
                'delivery_note_id' => $sj->id,
                'order_detail_id' => 1, // sementara isi dummy kalau belum pakai order
            ]);
        }

        DB::commit();

        return redirect()->back()->with('success','Surat Jalan Masuk berhasil disimpan');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors($e->getMessage());
    }
}



public function storeKeluar(Request $request)
{
    $request->validate([
        'tgl' => 'required|date',
        'order_id' => 'required',
    ]);

    DB::beginTransaction();

    try {

        $order = Orders::with('details')->findOrFail($request->order_id);

        $sj = DeliveryNote::create([
            'no' => 'SJ-K-' . now()->format('YmdHis'),
            'tgl' => $request->tgl,
            'type' => 'keluar',
            'order_id' => $order->id,
        ]);

        foreach ($order->details as $detail) {

            $barang = $detail->barang;

            if ($barang->stok < $detail->qty) {
                throw new \Exception('Stok tidak cukup untuk '.$barang->nama_barang);
            }

            $barang->stok -= $detail->qty;
            $barang->save();

            DeliveryNoteDetail::create([
                'delivery_note_id' => $sj->id,
                'order_detail_id' => $detail->id,
            ]);
        }

        DB::commit();

        return redirect()->back()->with('success','Surat Jalan Keluar berhasil disimpan');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors($e->getMessage());
    }
}



    public function detail($id)
    {
        $sj = DeliveryNote::with('details.orderDetail.barang', 'order.customer')
                ->findOrFail($id);

        return response()->json([
            'no_sj' => $sj->no,
            'customer' => $sj->order->customer->nama_customer ?? '-',
            'details' => $sj->details->map(function ($d) {
                return [
                    'barang' => $d->orderDetail->barang->nama_barang ?? '-',
                    'qty' => $d->orderDetail->qty ?? 0
                ];
            })
        ]);
    }
}
