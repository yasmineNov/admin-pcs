<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use App\Models\MutasiBarang;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    public function index()
    {
        $data = DeliveryNote::with('customer')->orderBy('tgl_sj', 'desc')->get();
        return view('penjualan.surat-jalan.index', compact('data'));
    }

    public function create()
{
    $customers = \App\Models\Customer::all();
    $barangs = \App\Models\Barang::all();
    $orders = \App\Models\Orders::with('customer','details.barang')
                ->where('status','approved') // optional
                ->get();
    return view('penjualan.surat-jalan.create', compact('customers', 'orders','barangs'));
}

    public function store(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'tgl_sj' => 'required'
    ]);

    DB::transaction(function () use ($request) {

        $order = Orders::with('details.barang')
                    ->findOrFail($request->order_id);

        $deliveryNote = DeliveryNote::create([
            'no' => 'SJ-' . time(),
            'tgl' => $request->tgl_sj,
            'keterangan' => null,
            'alamat_kirim' => null,
            'order_id' => $order->id, // WAJIB ADA
        ]);

        foreach ($request->qty as $detailId => $qtyKirim) {

            if ($qtyKirim <= 0) continue;

            $orderDetail = $order->details->where('id', $detailId)->first();

            if (!$orderDetail) continue;

            if ($qtyKirim > $orderDetail->qty) {
                throw new \Exception("Qty kirim melebihi qty order!");
            }

            $barang = $orderDetail->barang;

            if ($barang->stok < $qtyKirim) {
                throw new \Exception("Stok {$barang->nama_barang} tidak cukup!");
            }

            DeliveryNoteDetail::create([
                'delivery_note_id' => $deliveryNote->id,
                'order_detail_id' => $orderDetail->id,
                'keterangan' => null,
            ]);

            MutasiBarang::create([
                'tgl_mutasi' => now(),
                'barang_id' => $barang->id,
                'qty' => $qtyKirim,
                'tipe' => 'OUT',
                'keterangan' => 'Surat Jalan ' . $deliveryNote->no,
            ]);

            $barang->decrement('stok', $qtyKirim);
        }

    });

    return redirect()->route('surat-jalan.index')
        ->with('success','Surat Jalan berhasil dibuat');

}

    public function detail($id)
    {
        $sj = DeliveryNote::with('details.orderDetail.barang', 'order.customer')
                ->findOrFail($id);

        return response()->json([
            'no_sj' => $sj->no,
            'customer' => $sj->order->customer,
            'details' => $sj->details->map(function ($d) {
                return [
                    'barang' => $d->orderDetail->barang,
                    'qty' => $d->orderDetail->qty
                ];
            })
        ]);
    }
}
